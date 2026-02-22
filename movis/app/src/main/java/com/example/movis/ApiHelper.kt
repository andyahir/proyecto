package com.example.movis

import android.os.Handler
import android.os.Looper
import org.json.JSONObject
import java.io.BufferedReader
import java.io.InputStreamReader
import java.io.OutputStreamWriter
import java.net.HttpURLConnection
import java.net.URL

object ApiHelper {

    private val mainHandler = Handler(Looper.getMainLooper())

    // ── POST request ────────────────────────────────────────
    fun post(
        urlStr: String,
        jsonBody: JSONObject,
        token: String? = null,
        onSuccess: (JSONObject) -> Unit,
        onError: (String) -> Unit
    ) {
        Thread {
            try {
                val url = URL(urlStr)
                val conn = url.openConnection() as HttpURLConnection
                conn.requestMethod = "POST"
                conn.setRequestProperty("Content-Type", "application/json; charset=utf-8")
                conn.setRequestProperty("Accept", "application/json")
                if (token != null) {
                    conn.setRequestProperty("Authorization", "Bearer $token")
                }
                conn.doOutput = true
                conn.connectTimeout = 10000
                conn.readTimeout = 10000

                val writer = OutputStreamWriter(conn.outputStream, "UTF-8")
                writer.write(jsonBody.toString())
                writer.flush()
                writer.close()

                val responseCode = conn.responseCode
                val stream = if (responseCode in 200..299) conn.inputStream else conn.errorStream
                val reader = BufferedReader(InputStreamReader(stream, "UTF-8"))
                val response = reader.readText()
                reader.close()
                conn.disconnect()

                val json = JSONObject(response)
                mainHandler.post {
                    if (json.optBoolean("success", false)) {
                        onSuccess(json.getJSONObject("data"))
                    } else {
                        onError(json.optString("error", "Error desconocido"))
                    }
                }
            } catch (e: Exception) {
                mainHandler.post {
                    onError("Error de conexión: ${e.localizedMessage}")
                }
            }
        }.start()
    }

    // ── GET request ─────────────────────────────────────────
    fun get(
        urlStr: String,
        token: String? = null,
        onSuccess: (JSONObject) -> Unit,
        onError: (String) -> Unit
    ) {
        Thread {
            try {
                val url = URL(urlStr)
                val conn = url.openConnection() as HttpURLConnection
                conn.requestMethod = "GET"
                conn.setRequestProperty("Accept", "application/json")
                if (token != null) {
                    conn.setRequestProperty("Authorization", "Bearer $token")
                }
                conn.connectTimeout = 10000
                conn.readTimeout = 10000

                val responseCode = conn.responseCode
                val stream = if (responseCode in 200..299) conn.inputStream else conn.errorStream
                val reader = BufferedReader(InputStreamReader(stream, "UTF-8"))
                val response = reader.readText()
                reader.close()
                conn.disconnect()

                val json = JSONObject(response)
                mainHandler.post {
                    if (json.optBoolean("success", false)) {
                        onSuccess(json.getJSONObject("data"))
                    } else {
                        onError(json.optString("error", "Error desconocido"))
                    }
                }
            } catch (e: Exception) {
                mainHandler.post {
                    onError("Error de conexión: ${e.localizedMessage}")
                }
            }
        }.start()
    }
}
