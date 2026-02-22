package com.example.movis

import android.os.Bundle
import android.view.View
import android.widget.Button
import android.widget.EditText
import android.widget.ProgressBar
import android.widget.TextView
import android.widget.Toast
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import org.json.JSONObject

class RegisterActivity : AppCompatActivity() {

    private lateinit var etNombre: EditText
    private lateinit var etApellidoP: EditText
    private lateinit var etApellidoM: EditText
    private lateinit var etCorreo: EditText
    private lateinit var etPassword: EditText
    private lateinit var btnRegistrar: Button
    private lateinit var tvError: TextView
    private lateinit var tvGoLogin: TextView
    private lateinit var progressBar: ProgressBar

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContentView(R.layout.activity_register)

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(android.R.id.content)) { v, insets ->
            val bars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            v.setPadding(bars.left, bars.top, bars.right, bars.bottom)
            insets
        }

        // Bind views
        etNombre = findViewById(R.id.etNombre)
        etApellidoP = findViewById(R.id.etApellidoP)
        etApellidoM = findViewById(R.id.etApellidoM)
        etCorreo = findViewById(R.id.etCorreo)
        etPassword = findViewById(R.id.etPassword)
        btnRegistrar = findViewById(R.id.btnRegistrar)
        tvError = findViewById(R.id.tvError)
        tvGoLogin = findViewById(R.id.tvGoLogin)
        progressBar = findViewById(R.id.progressBar)

        // Registrar
        btnRegistrar.setOnClickListener { doRegister() }

        // Volver a login
        tvGoLogin.setOnClickListener { finish() }
    }

    private fun doRegister() {
        val nombre = etNombre.text.toString().trim()
        val apellidoP = etApellidoP.text.toString().trim()
        val apellidoM = etApellidoM.text.toString().trim()
        val correo = etCorreo.text.toString().trim()
        val password = etPassword.text.toString().trim()

        // Validaciones
        if (nombre.isEmpty()) {
            showError("Ingresa tu nombre")
            return
        }
        if (apellidoP.isEmpty()) {
            showError("Ingresa tu apellido paterno")
            return
        }
        if (correo.isEmpty()) {
            showError("Ingresa tu correo electrónico")
            return
        }
        if (password.isEmpty()) {
            showError("Ingresa una contraseña")
            return
        }
        if (password.length < 4) {
            showError("La contraseña debe tener al menos 4 caracteres")
            return
        }

        setLoading(true)
        hideError()

        val body = JSONObject().apply {
            put("nombre", nombre)
            put("apellido_paterno", apellidoP)
            put("apellido_materno", apellidoM)
            put("correo", correo)
            put("password", password)
        }

        ApiHelper.post(
            urlStr = ApiConfig.registroUrl(),
            jsonBody = body,
            onSuccess = { data ->
                setLoading(false)
                val mensaje = data.optString("mensaje", "Registro exitoso")
                val clave = data.optString("clave", "")
                val msg = if (clave.isNotEmpty()) "$mensaje\nTu clave: $clave" else mensaje
                Toast.makeText(this, msg, Toast.LENGTH_LONG).show()
                finish() // Volver al login
            },
            onError = { error ->
                setLoading(false)
                showError(error)
            }
        )
    }

    private fun setLoading(loading: Boolean) {
        progressBar.visibility = if (loading) View.VISIBLE else View.GONE
        btnRegistrar.isEnabled = !loading
    }

    private fun showError(msg: String) {
        tvError.text = msg
        tvError.visibility = View.VISIBLE
    }

    private fun hideError() {
        tvError.visibility = View.GONE
    }
}
