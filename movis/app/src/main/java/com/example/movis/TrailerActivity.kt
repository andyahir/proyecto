package com.example.movis

import android.os.Bundle
import android.view.View
import android.webkit.WebChromeClient
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import android.widget.Button
import android.widget.TextView
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat

class TrailerActivity : AppCompatActivity() {

    private lateinit var webView: WebView

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContentView(R.layout.activity_trailer)

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main)) { v, insets ->
            val bars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            v.setPadding(bars.left, bars.top, bars.right, bars.bottom)
            insets
        }

        val movieName = intent.getStringExtra("movie_name") ?: "Película"
        val trailerUrl = intent.getStringExtra("trailer_url") ?: ""

        val tvMovieName: TextView = findViewById(R.id.tvMovieName)
        val btnBack: Button = findViewById(R.id.btnBack)
        webView = findViewById(R.id.webViewTrailer)

        tvMovieName.text = movieName
        btnBack.setOnClickListener { finish() }

        // Configurar WebView
        val settings = webView.settings
        settings.javaScriptEnabled = true
        settings.domStorageEnabled = true
        settings.mediaPlaybackRequiresUserGesture = false
        settings.loadWithOverviewMode = true
        settings.useWideViewPort = true

        webView.webChromeClient = WebChromeClient()
        webView.webViewClient = WebViewClient()

        // Extraer video ID de YouTube y reproducir con embed
        val videoId = extractYouTubeId(trailerUrl)
        if (videoId != null) {
            val embedHtml = """
                <!DOCTYPE html>
                <html>
                <head>
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <style>
                        * { margin: 0; padding: 0; }
                        body { background: #000; display: flex; align-items: center; justify-content: center; height: 100vh; }
                        .video-container { position: relative; width: 100%; padding-bottom: 56.25%; }
                        .video-container iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; }
                    </style>
                </head>
                <body>
                    <div class="video-container">
                        <iframe 
                            src="https://www.youtube.com/embed/$videoId?autoplay=1&rel=0&modestbranding=1" 
                            allow="autoplay; encrypted-media; fullscreen"
                            allowfullscreen>
                        </iframe>
                    </div>
                </body>
                </html>
            """.trimIndent()
            webView.loadData(embedHtml, "text/html", "UTF-8")
        } else {
            // Si no es YouTube, abrir la URL directo
            webView.loadUrl(trailerUrl)
        }
    }

    private fun extractYouTubeId(url: String): String? {
        // Soportar: youtube.com/watch?v=ID, youtu.be/ID, youtube.com/embed/ID
        val patterns = listOf(
            Regex("""youtube\.com/watch\?v=([a-zA-Z0-9_-]+)"""),
            Regex("""youtu\.be/([a-zA-Z0-9_-]+)"""),
            Regex("""youtube\.com/embed/([a-zA-Z0-9_-]+)""")
        )
        for (pattern in patterns) {
            val match = pattern.find(url)
            if (match != null) return match.groupValues[1]
        }
        return null
    }

    override fun onBackPressed() {
        if (webView.canGoBack()) {
            webView.goBack()
        } else {
            super.onBackPressed()
        }
    }

    override fun onDestroy() {
        webView.destroy()
        super.onDestroy()
    }
}
