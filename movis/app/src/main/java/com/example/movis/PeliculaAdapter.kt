package com.example.movis

import android.content.Intent
import android.net.Uri
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.ImageView
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView

class PeliculaAdapter(
    private var peliculas: List<Pelicula> = emptyList()
) : RecyclerView.Adapter<PeliculaAdapter.ViewHolder>() {

    class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        val ivPoster: ImageView = view.findViewById(R.id.ivPoster)
        val tvNombre: TextView = view.findViewById(R.id.tvNombre)
        val tvGenero: TextView = view.findViewById(R.id.tvGenero)
        val tvDescripcion: TextView = view.findViewById(R.id.tvDescripcion)
        val btnTrailer: Button = view.findViewById(R.id.btnTrailer)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_pelicula, parent, false)
        return ViewHolder(view)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val pelicula = peliculas[position]

        holder.tvNombre.text = pelicula.nombre
        holder.tvGenero.text = pelicula.genero
        holder.tvDescripcion.text = pelicula.descripcion ?: "Sin descripción"

        // Cargar imagen
        ImageLoader.load(pelicula.imagen, holder.ivPoster)

        // Boton Ver Pelicula → abrir YouTube directamente
        if (!pelicula.trailerUrl.isNullOrBlank()) {
            holder.btnTrailer.visibility = View.VISIBLE
            holder.btnTrailer.setOnClickListener {
                val intent = Intent(Intent.ACTION_VIEW, Uri.parse(pelicula.trailerUrl))
                holder.itemView.context.startActivity(intent)
            }
        } else {
            holder.btnTrailer.visibility = View.GONE
        }
    }

    override fun getItemCount(): Int = peliculas.size

    fun updateList(newList: List<Pelicula>) {
        peliculas = newList
        notifyDataSetChanged()
    }
}
