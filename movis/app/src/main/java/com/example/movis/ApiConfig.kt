package com.example.movis

object ApiConfig {
    // ═══════════════════════════════════════════════════════════════
    // CAMBIA ESTA URL POR LA IP DE TU COMPUTADORA O SERVIDOR
    // ═══════════════════════════════════════════════════════════════
    // Emulador Android Studio → "10.0.2.2"
    // Dispositivo fisico      → IP de tu PC (ej. "192.168.1.100")
    // Servidor Hostinger      → tu dominio
    // ═══════════════════════════════════════════════════════════════
    private const val BASE_HOST = "silver-crane-179692.hostingersite.com"

    private const val BASE_URL = "https://$BASE_HOST"

    private const val API_URL  = "$BASE_URL/api/index.php"

    fun loginUrl()      = "$API_URL?endpoint=login"
    fun registroUrl()   = "$API_URL?endpoint=registro"
    fun peliculasUrl()  = "$API_URL?endpoint=peliculas"
    fun peliculaUrl(id: Int) = "$API_URL?endpoint=pelicula&id=$id"
    fun logoutUrl()     = "$API_URL?endpoint=logout"

    // URL de la pagina web para registrarse (se abre en el navegador)
    fun registerWebUrl() = "$BASE_URL/registro_cliente.php"
}
