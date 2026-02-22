package com.example.movis

data class Pelicula(
    val id: Int,
    val nombre: String,
    val genero: String,
    val imagen: String?,
    val descripcion: String?,
    val trailerUrl: String?
)
