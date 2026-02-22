SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `administradores` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido_paterno` varchar(100) NOT NULL,
  `apellido_materno` varchar(100) DEFAULT NULL,
  `correo` varchar(255) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `estatus` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `administradores` (`id`, `nombre`, `apellido_paterno`, `apellido_materno`, `correo`, `usuario`, `password_hash`, `estatus`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'Principal', 'Sistema', 'admin@streaming.com', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo', '2026-02-18 00:57:17', '2026-02-18 00:57:17'),
(2, 'andy', 'Gomez', 'Marquez', 'andy@gmail.com', 'andy', '$2y$10$t8HFrSiSjFdasYmESwvY7uychGAw2YSUWZX9zhQlRVqj02y0DG4/O', 'activo', '2026-02-18 01:04:40', '2026-02-18 01:04:40');

CREATE TABLE `clientes` (
  `id` int(10) UNSIGNED NOT NULL,
  `clave` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido_paterno` varchar(100) NOT NULL,
  `apellido_materno` varchar(100) DEFAULT NULL,
  `correo` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `estatus` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `clientes` (`id`, `clave`, `nombre`, `apellido_paterno`, `apellido_materno`, `correo`, `password_hash`, `estatus`, `created_at`, `updated_at`) VALUES
(1, 'CLI-C51088', 'Bernardo Medina', 'Medina', 'Sanchez', 'bernardobdms20@gmail.com', '$2y$10$pRgolqiGj/odzx/E6k7xVebx3BiLfqlEUyTx4IM3NLebdTaPJ2Riy', 'inactivo', '2026-02-18 01:02:17', '2026-02-18 01:02:52');

DELIMITER $$
CREATE TRIGGER `gen_clave_cliente` BEFORE INSERT ON `clientes` FOR EACH ROW BEGIN
  SET NEW.clave = CONCAT('CLI-', UPPER(SUBSTRING(MD5(RAND()), 1, 6)));
END
$$
DELIMITER ;

CREATE TABLE `generos` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `generos` (`id`, `nombre`, `created_at`) VALUES
(1, 'Acción', '2026-02-18 00:57:16'),
(2, 'Comedia', '2026-02-18 00:57:16'),
(3, 'Drama', '2026-02-18 00:57:16'),
(4, 'Terror', '2026-02-18 00:57:16'),
(5, 'Ciencia Ficción', '2026-02-18 00:57:16'),
(6, 'Animación', '2026-02-18 00:57:16'),
(7, 'Romance', '2026-02-18 00:57:16'),
(8, 'Thriller', '2026-02-18 00:57:16'),
(9, 'Documental', '2026-02-18 00:57:16'),
(10, 'Aventura', '2026-02-18 00:57:16');

CREATE TABLE `peliculas` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `genero_id` int(10) UNSIGNED NOT NULL,
  `imagen` varchar(500) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `trailer_url` varchar(500) DEFAULT NULL,
  `estatus` enum('activa','inactiva') NOT NULL DEFAULT 'activa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `peliculas` (`id`, `nombre`, `genero_id`, `imagen`, `descripcion`, `trailer_url`, `estatus`, `created_at`, `updated_at`) VALUES
(1, 'Mad Max: Fury Road', 1, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSBvRE6nWENju4ihjFciONnr5nEVf9I0jK4OhG8wI6kSag7A6lhUxTyxcqzhEZp6x2yn49uEqU4APBntFc4-ZKcKx_WvMHRE4DOykmqYw&s=10', 'En un mundo postapocalíptico, Max se une a Furiosa en una huida desesperada a través del desierto contra el tiránico Immortan Joe y su ejército.', 'https://www.youtube.com/watch?v=hEJnMQG9ev8', 'activa', '2026-02-18 01:10:00', '2026-02-18 01:11:49'),
(2, 'John Wick', 1, 'https://image.tmdb.org/t/p/w500/fZPSd91yGE9fCcCe6OoQr6E3Bev.jpg', 'Un exasesino legendario regresa al inframundo criminal para vengarse de quienes mataron a su perro, el último recuerdo de su esposa fallecida.', 'https://www.youtube.com/watch?v=2AUmvWm5ZDQ', 'activa', '2026-02-18 01:10:00', '2026-02-18 01:10:00'),
(3, 'Superbad', 2, 'https://m.media-amazon.com/images/I/61nHD0I77KL._AC_UF894,1000_QL80_.jpg', 'Dos amigos inseparables intentan conseguir alcohol para una fiesta antes de que termine el bachillerato, desencadenando una noche de caos absoluto.', 'https://www.youtube.com/watch?v=4eGMmd0HEOE', 'activa', '2026-02-18 01:10:00', '2026-02-18 01:12:22'),
(4, 'La Resaca', 2, 'https://image.tmdb.org/t/p/w500/uluhlXubGu1VxU63X9VHCLWDAYP.jpg', 'Cuatro amigos viajan a Las Vegas para una despedida de soltero. Al despertar sin recuerdos y sin el novio, deben reconstruir la noche más loca de sus vidas.', 'https://www.youtube.com/watch?v=LBgtf8FoTb8', 'activa', '2026-02-18 01:10:00', '2026-02-18 01:27:01'),
(5, 'El Padrino', 3, 'https://m.media-amazon.com/images/M/MV5BZmNiNzM4MTctODI5YS00MzczLWE2MzktNzY4YmNjYjA5YmY1XkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'La historia de la familia Corleone, una poderosa dinastía criminal italiana en Nueva York, y la transformación de Michael, el hijo menor, en su sucesor.', 'https://www.youtube.com/watch?v=sY1S34973zA', 'activa', '2026-02-18 01:10:00', '2026-02-18 01:12:54'),
(6, 'Forrest Gump', 3, 'https://image.tmdb.org/t/p/w500/saHP97rTPS5eLmrLQEcANmKrsFl.jpg', 'A través de los ojos de Forrest, un hombre de Alabama con bajo coeficiente intelectual pero gran corazón, se repasan los eventos más importantes de la historia americana.', 'https://www.youtube.com/watch?v=bLvqoHBptjg', 'activa', '2026-02-18 01:10:00', '2026-02-18 01:10:00'),
(7, 'Jeepers Creepers', 4, 'https://m.media-amazon.com/images/M/MV5BMTkwNDU0NTE0OV5BMl5BanBnXkFtZTgwNzAzNzQyMTI@._V1_.jpg', 'Trish y Darry, dos hermanos universitarios, viajan por carretera y sintonizan una canción que anuncia la presencia de una criatura aterradora que los persigue.', 'https://www.youtube.com/watch?v=UBoGklz1wGM', 'activa', '2026-02-18 01:10:00', '2026-02-18 01:25:25'),
(8, 'It 2', 4, 'https://m.media-amazon.com/images/I/81LBOEE0frL.jpg', 'Un grupo de niños en Derry, Maine, se enfrenta a Pennywise, un payaso demoníaco que resurge cada 27 años para aterrorizar y devorar a sus víctimas.', 'https://www.youtube.com/watch?v=FnCdOQsX5kc', 'activa', '2026-02-18 01:10:00', '2026-02-18 01:14:15'),
(9, 'Interstellar', 5, 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg', 'Un equipo de astronautas viaja a través de un agujero de gusano en busca de un nuevo planeta habitable para salvar a la humanidad del colapso de la Tierra.', 'https://www.youtube.com/watch?v=zSWdZVtXT7E', 'activa', '2026-02-18 01:10:00', '2026-02-18 01:10:00'),
(10, 'Matrix', 5, 'https://image.tmdb.org/t/p/w500/f89U3ADr1oiB1s9GkdPOEpXUk5H.jpg', 'Neo descubre que la realidad que conoce es una simulación controlada por máquinas. Se une a un grupo de rebeldes para luchar por la libertad de la humanidad.', 'https://www.youtube.com/watch?v=vKQi3bBA1y8', 'activa', '2026-02-18 01:10:00', '2026-02-18 01:10:00'),
(11, 'El Rey León', 6, 'https://lumiere-a.akamaihd.net/v1/images/image_8b5ca578.jpeg', 'Simba, el cachorro de un rey león, huye tras la muerte de su padre y debe regresar para reclamar su lugar en el reino de la Roca del Orgullo.', 'https://www.youtube.com/watch?v=4sj1MT05lAA', 'activa', '2026-02-18 01:10:00', '2026-02-18 01:14:41'),
(12, 'Toy Story', 6, 'https://image.tmdb.org/t/p/w500/uXDfjJbdP4ijW5hWSBrPrlKpxab.jpg', 'Woody, un vaquero de juguete, ve amenazado su lugar favorito con Andy cuando llega Buzz Lightyear, un moderno astronauta que se convierte en su rival.', 'https://www.youtube.com/watch?v=ehfyxhWeE24', 'activa', '2026-02-18 01:10:00', '2026-02-18 01:27:52'),
(13, 'Titanic', 7, 'https://image.tmdb.org/t/p/w500/9xjZS2rlVxm8SFx8kPC3aIGCOYQ.jpg', 'Jack y Rose, de clases sociales opuestas, se enamoran a bordo del Titanic en su viaje inaugural, mientras el barco se dirige a su trágico destino.', 'https://www.youtube.com/watch?v=CHekzSiZjrY', 'activa', '2026-02-18 01:10:00', '2026-02-18 01:10:00'),
(14, 'Diario de una Pasión', 7, 'https://m.media-amazon.com/images/M/MV5BM2RiMzcxYmYtNzQ3MC00NTQ4LWE0ZjktNGUwODI1MzhjNDNkXkEyXkFqcGc@._V1_.jpg', 'Noah y Allie se enamoran en un verano de juventud, pero las diferencias de clase y la guerra los separan. Años después, su historia de amor es puesta a prueba.', 'https://www.youtube.com/watch?v=BjJcYdEOI0k', 'activa', '2026-02-18 01:10:00', '2026-02-18 01:23:18'),
(15, 'El Silencio de los Inocentes', 8, 'https://image.tmdb.org/t/p/w500/uS9m8OBk1A8eM9I042bx8XXpqAq.jpg', 'Una joven agente del FBI busca la ayuda del brillante pero perturbador psiquiatra Hannibal Lecter para capturar a un asesino serial.', 'https://www.youtube.com/watch?v=W6Mm8Sbe__o', 'activa', '2026-02-18 01:10:00', '2026-02-18 01:10:00'),
(16, 'Shutter Island', 8, 'https://m.media-amazon.com/images/I/916VtXkyrHL._AC_UF894,1000_QL80_.jpg', 'Un marshal investiga la desaparición de una paciente en un hospital psiquiátrico de máxima seguridad en una isla, pero nada es lo que parece.', 'https://www.youtube.com/watch?v=5iaYLCiq5RM', 'activa', '2026-02-18 01:10:00', '2026-02-18 01:16:23'),
(17, 'Free Solo', 9, 'https://pics.filmaffinity.com/Free_Solo-205471290-large.jpg', 'Alex Honnold intenta escalar en solitario y sin cuerdas El Capitan en el Parque Yosemite, una proeza que nadie ha logrado antes y que podría costarle la vida.', 'https://www.youtube.com/watch?v=urRVZ4SW7WU', 'activa', '2026-02-18 01:10:00', '2026-02-18 01:16:48'),
(18, 'Planeta Tierra II', 9, 'https://m.media-amazon.com/images/M/MV5BMzY4NDBkMWYtYzdkYy00YzBjLWJmODctMWM4YjYzZTdjNWE5XkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'David Attenborough guía al espectador a través de los ecosistemas más espectaculares del planeta: islas, montañas, junglas, desiertos y ciudades.', 'https://www.youtube.com/watch?v=c8aFcHFu8QM', 'activa', '2026-02-18 01:10:00', '2026-02-18 01:17:23'),
(19, 'Indiana Jones y los Cazadores del Arca Perdida', 10, 'https://image.tmdb.org/t/p/w500/ceG9VzoRAVGwivFU403Wc3AHRys.jpg', 'El arqueólogo aventurero Indiana Jones debe encontrar el Arca de la Alianza antes que los nazis, en una carrera que lo lleva desde Nepal hasta Egipto.', 'https://www.youtube.com/watch?v=ekD0PzSUVDI', 'activa', '2026-02-18 01:10:00', '2026-02-18 01:24:29'),
(20, 'Jurassic Park', 10, 'https://image.tmdb.org/t/p/w500/oU7Oq2kFAAlGqbU4VoAE36g4hoI.jpg', 'Un millonario abre un parque temático con dinosaurios clonados. Cuando el sistema de seguridad falla, los visitantes deben sobrevivir a las criaturas prehistóricas.', 'https://www.youtube.com/watch?v=lc0UehYemQA', 'activa', '2026-02-18 01:10:00', '2026-02-18 01:10:00');

CREATE TABLE `tokens_api` (
  `id` int(10) UNSIGNED NOT NULL,
  `cliente_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `usuario` (`usuario`);

ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`),
  ADD UNIQUE KEY `correo` (`correo`);

ALTER TABLE `generos`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `peliculas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pelicula_genero` (`genero_id`);

ALTER TABLE `tokens_api`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `fk_token_cliente` (`cliente_id`);

ALTER TABLE `administradores`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;


ALTER TABLE `clientes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `generos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `peliculas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;


ALTER TABLE `tokens_api`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `peliculas`
  ADD CONSTRAINT `fk_pelicula_genero` FOREIGN KEY (`genero_id`) REFERENCES `generos` (`id`) ON UPDATE CASCADE;


ALTER TABLE `tokens_api`
  ADD CONSTRAINT `fk_token_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;
COMMIT;

