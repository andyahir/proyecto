package com.example.movis

import android.content.Context
import android.content.SharedPreferences

class SessionManager(context: Context) {

    private val prefs: SharedPreferences =
        context.getSharedPreferences("movis_session", Context.MODE_PRIVATE)

    companion object {
        private const val KEY_TOKEN = "token"
        private const val KEY_NOMBRE = "nombre"
        private const val KEY_CORREO = "correo"
        private const val KEY_LOGGED_IN = "logged_in"
    }

    fun saveLogin(token: String, nombre: String, correo: String) {
        prefs.edit()
            .putString(KEY_TOKEN, token)
            .putString(KEY_NOMBRE, nombre)
            .putString(KEY_CORREO, correo)
            .putBoolean(KEY_LOGGED_IN, true)
            .apply()
    }

    fun getToken(): String? = prefs.getString(KEY_TOKEN, null)

    fun getNombre(): String = prefs.getString(KEY_NOMBRE, "") ?: ""

    fun isLoggedIn(): Boolean = prefs.getBoolean(KEY_LOGGED_IN, false)

    fun logout() {
        prefs.edit().clear().apply()
    }
}
