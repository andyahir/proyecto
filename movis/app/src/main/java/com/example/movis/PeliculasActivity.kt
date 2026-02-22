package com.example.movis

import android.content.Intent
import android.os.Bundle
import android.text.Editable
import android.text.TextWatcher
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
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import org.json.JSONObject

class PeliculasActivity : AppCompatActivity() {

    private lateinit var rvPeliculas: RecyclerView
    private lateinit var etSearch: EditText
    private lateinit var tvSaludo: TextView
    private lateinit var tvEmpty: TextView
    private lateinit var btnLogout: Button
    private lateinit var progressBar: ProgressBar
    private lateinit var session: SessionManager
    private lateinit var adapter: PeliculaAdapter

    private var allPeliculas: List<Pelicula> = emptyList()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContentView(R.layout.activity_peliculas)

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main)) { v, insets ->
            val bars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            v.setPadding(bars.left, bars.top, bars.right, bars.bottom)
            insets
        }

        session = SessionManager(this)

        // Verificar sesión
        if (!session.isLoggedIn()) {
            goToLogin()
            return
        }

        // Bind views
        rvPeliculas = findViewById(R.id.rvPeliculas)
        etSearch = findViewById(R.id.etSearch)
        tvSaludo = findViewById(R.id.tvSaludo)
        tvEmpty = findViewById(R.id.tvEmpty)
        btnLogout = findViewById(R.id.btnLogout)
        progressBar = findViewById(R.id.progressBar)

        // Saludo
        tvSaludo.text = getString(R.string.hola_usuario, session.getNombre())

        // RecyclerView
        adapter = PeliculaAdapter()
        rvPeliculas.layoutManager = LinearLayoutManager(this)
        rvPeliculas.adapter = adapter

        // Buscar
        etSearch.addTextChangedListener(object : TextWatcher {
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {}
            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {}
            override fun afterTextChanged(s: Editable?) {
                filterPeliculas(s.toString())
            }
        })

        // Logout
        btnLogout.setOnClickListener { doLogout() }

        // Cargar películas
        loadPeliculas()
    }

    private fun loadPeliculas() {
        progressBar.visibility = View.VISIBLE
        tvEmpty.visibility = View.GONE
        rvPeliculas.visibility = View.GONE

        val token = session.getToken() ?: run {
            goToLogin()
            return
        }

        ApiHelper.get(
            urlStr = ApiConfig.peliculasUrl(),
            token = token,
            onSuccess = { data ->
                progressBar.visibility = View.GONE

                val jsonArray = data.getJSONArray("peliculas")
                val list = mutableListOf<Pelicula>()

                for (i in 0 until jsonArray.length()) {
                    val obj = jsonArray.getJSONObject(i)
                    list.add(
                        Pelicula(
                            id = obj.getInt("id"),
                            nombre = obj.getString("nombre"),
                            genero = obj.optString("genero", ""),
                            imagen = obj.optString("imagen", null),
                            descripcion = obj.optString("descripcion", null),
                            trailerUrl = obj.optString("trailer_url", null)
                        )
                    )
                }

                allPeliculas = list
                adapter.updateList(list)

                if (list.isEmpty()) {
                    tvEmpty.visibility = View.VISIBLE
                    rvPeliculas.visibility = View.GONE
                } else {
                    tvEmpty.visibility = View.GONE
                    rvPeliculas.visibility = View.VISIBLE
                }
            },
            onError = { error ->
                progressBar.visibility = View.GONE
                tvEmpty.text = error
                tvEmpty.visibility = View.VISIBLE

                // Si el token expiró, cerrar sesión
                if (error.contains("Token", ignoreCase = true)) {
                    session.logout()
                    goToLogin()
                }
            }
        )
    }

    private fun filterPeliculas(query: String) {
        if (query.isBlank()) {
            adapter.updateList(allPeliculas)
            tvEmpty.visibility = if (allPeliculas.isEmpty()) View.VISIBLE else View.GONE
            rvPeliculas.visibility = if (allPeliculas.isEmpty()) View.GONE else View.VISIBLE
            return
        }

        val filtered = allPeliculas.filter { pelicula ->
            pelicula.nombre.contains(query, ignoreCase = true) ||
            pelicula.genero.contains(query, ignoreCase = true)
        }

        adapter.updateList(filtered)
        tvEmpty.visibility = if (filtered.isEmpty()) View.VISIBLE else View.GONE
        rvPeliculas.visibility = if (filtered.isEmpty()) View.GONE else View.VISIBLE
    }

    private fun doLogout() {
        val token = session.getToken()
        session.logout()

        // Llamar API de logout (en segundo plano, no bloquear)
        if (token != null) {
            ApiHelper.post(
                urlStr = ApiConfig.logoutUrl(),
                jsonBody = JSONObject(),
                token = token,
                onSuccess = { },
                onError = { }
            )
        }

        Toast.makeText(this, "Sesión cerrada", Toast.LENGTH_SHORT).show()
        goToLogin()
    }

    private fun goToLogin() {
        val intent = Intent(this, LoginActivity::class.java)
        intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
        startActivity(intent)
        finish()
    }
}
