package com.example.movis

import android.graphics.Bitmap
import android.graphics.BitmapFactory
import android.os.Handler
import android.os.Looper
import android.util.LruCache
import android.widget.ImageView
import java.net.HttpURLConnection
import java.net.URL
import java.util.concurrent.Executors

object ImageLoader {

    private val executor = Executors.newFixedThreadPool(4)
    private val mainHandler = Handler(Looper.getMainLooper())

    // Cache en memoria (1/8 de la memoria disponible)
    private val maxMemory = (Runtime.getRuntime().maxMemory() / 1024).toInt()
    private val cacheSize = maxMemory / 8
    private val cache = object : LruCache<String, Bitmap>(cacheSize) {
        override fun sizeOf(key: String, bitmap: Bitmap): Int {
            return bitmap.byteCount / 1024
        }
    }

    fun load(url: String?, imageView: ImageView, placeholderRes: Int = 0) {
        if (url.isNullOrBlank()) {
            if (placeholderRes != 0) imageView.setImageResource(placeholderRes)
            return
        }

        // Tag para evitar reciclaje incorrecto en RecyclerView
        imageView.tag = url

        // Intentar desde cache
        val cached = cache.get(url)
        if (cached != null) {
            imageView.setImageBitmap(cached)
            return
        }

        // Placeholder mientras carga
        if (placeholderRes != 0) imageView.setImageResource(placeholderRes)

        executor.execute {
            try {
                val connection = URL(url).openConnection() as HttpURLConnection
                connection.connectTimeout = 8000
                connection.readTimeout = 8000
                connection.instanceFollowRedirects = true
                connection.connect()

                val input = connection.inputStream
                val bitmap = BitmapFactory.decodeStream(input)
                input.close()
                connection.disconnect()

                if (bitmap != null) {
                    cache.put(url, bitmap)
                    mainHandler.post {
                        // Solo actualizar si el ImageView sigue mostrando esta URL
                        if (imageView.tag == url) {
                            imageView.setImageBitmap(bitmap)
                        }
                    }
                }
            } catch (_: Exception) {
                // Silenciar error de carga de imagen
            }
        }
    }
}
