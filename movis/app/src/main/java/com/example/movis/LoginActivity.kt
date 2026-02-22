package com.example.movis

import android.content.Intent
import android.net.Uri
import android.os.Bundle
import android.view.View
import android.widget.Button
import android.widget.EditText
import android.widget.ProgressBar
import android.widget.TextView
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import org.json.JSONObject

class LoginActivity : AppCompatActivity() {

    private lateinit var etCorreo: EditText
    private lateinit var etPassword: EditText
    private lateinit var btnLogin: Button
    private lateinit var btnGoRegister: Button
    private lateinit var tvError: TextView
    private lateinit var progressBar: ProgressBar
    private lateinit var session: SessionManager

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContentView(R.layout.activity_login)

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(android.R.id.content)) { v, insets ->
            val bars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            v.setPadding(bars.left, bars.top, bars.right, bars.bottom)
            insets
        }

        session = SessionManager(this)

        // Si ya tiene sesion activa, ir directo a peliculas
        if (session.isLoggedIn()) {
            goToPeliculas()
            return
        }

        // Bind views
        etCorreo = findViewById(R.id.etCorreo)
        etPassword = findViewById(R.id.etPassword)
        btnLogin = findViewById(R.id.btnLogin)
        btnGoRegister = findViewById(R.id.btnGoRegister)
        tvError = findViewById(R.id.tvError)
        progressBar = findViewById(R.id.progressBar)

        // Login
        btnLogin.setOnClickListener { doLogin() }

        // Registrarse → abre la pagina web en el navegador
        btnGoRegister.setOnClickListener {
            val registerUrl = ApiConfig.registerWebUrl()
            val intent = Intent(Intent.ACTION_VIEW, Uri.parse(registerUrl))
            startActivity(intent)
        }
    }

    private fun doLogin() {
        val correo = etCorreo.text.toString().trim()
        val password = etPassword.text.toString().trim()

        if (correo.isEmpty()) {
            showError("Ingresa tu correo electrónico")
            return
        }
        if (password.isEmpty()) {
            showError("Ingresa tu contraseña")
            return
        }

        setLoading(true)
        hideError()

        val body = JSONObject().apply {
            put("correo", correo)
            put("password", password)
        }

        ApiHelper.post(
            urlStr = ApiConfig.loginUrl(),
            jsonBody = body,
            onSuccess = { data ->
                val token = data.getString("token")
                val cliente = data.getJSONObject("cliente")
                val nombre = cliente.getString("nombre")
                val correoResp = cliente.getString("correo")

                session.saveLogin(token, nombre, correoResp)
                setLoading(false)
                goToPeliculas()
            },
            onError = { error ->
                setLoading(false)
                showError(error)
            }
        )
    }

    private fun goToPeliculas() {
        val intent = Intent(this, PeliculasActivity::class.java)
        intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
        startActivity(intent)
        finish()
    }

    private fun setLoading(loading: Boolean) {
        progressBar.visibility = if (loading) View.VISIBLE else View.GONE
        btnLogin.isEnabled = !loading
        btnGoRegister.isEnabled = !loading
    }

    private fun showError(msg: String) {
        tvError.text = msg
        tvError.visibility = View.VISIBLE
    }

    private fun hideError() {
        tvError.visibility = View.GONE
    }
}
