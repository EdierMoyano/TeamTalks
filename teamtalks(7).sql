-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 24, 2025 at 03:49 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `teamtalks`
--

-- --------------------------------------------------------

--
-- Table structure for table `clases`
--

CREATE TABLE `clases` (
  `Id_clase` int(11) NOT NULL,
  `Nom_clase` varchar(255) DEFAULT NULL,
  `Id_tarea` int(11) DEFAULT NULL,
  `Id_materia` int(11) DEFAULT NULL,
  `Id_user` int(11) DEFAULT NULL,
  `id_ficha` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clases`
--

INSERT INTO `clases` (`Id_clase`, `Nom_clase`, `Id_tarea`, `Id_materia`, `Id_user`, `id_ficha`) VALUES
(0, 'Clase ADSO', NULL, NULL, NULL, 17);

-- --------------------------------------------------------

--
-- Table structure for table `clases_tareas`
--

CREATE TABLE `clases_tareas` (
  `id_clase_tarea` int(11) NOT NULL,
  `id_clase` int(11) NOT NULL,
  `id_tarea` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `compartido`
--

CREATE TABLE `compartido` (
  `Id_compartido` int(11) NOT NULL,
  `Id_user` int(11) NOT NULL,
  `Fecha_comp` datetime NOT NULL,
  `Id_doc` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `documentos`
--

CREATE TABLE `documentos` (
  `Id_doc` int(11) NOT NULL,
  `Nom_doc` varchar(255) NOT NULL,
  `Documento` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `estado`
--

CREATE TABLE `estado` (
  `Id_estado` int(11) NOT NULL,
  `Tipo_estado` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `estado`
--

INSERT INTO `estado` (`Id_estado`, `Tipo_estado`) VALUES
(1, 'Activo'),
(2, 'Inactivo');

-- --------------------------------------------------------

--
-- Table structure for table `estado_tarea`
--

CREATE TABLE `estado_tarea` (
  `Id_tarea_estado` int(11) NOT NULL,
  `Estado_tarea` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fichas`
--

CREATE TABLE `fichas` (
  `id_ficha` int(11) NOT NULL,
  `numero_ficha` varchar(20) NOT NULL,
  `nombre_ficha` varchar(255) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fichas`
--

INSERT INTO `fichas` (`id_ficha`, `numero_ficha`, `nombre_ficha`, `fecha_creacion`) VALUES
(2, '2901879', 'ADSO', '2025-03-23 11:34:57'),
(3, '2801856', 'pinturassss', '2025-03-23 11:49:51'),
(17, '2901878', 'TELECOMUNCACIONES', '2025-03-23 21:09:13'),
(18, '2901877', 'Moores', '2025-03-23 21:17:54');

-- --------------------------------------------------------

--
-- Table structure for table `foro`
--

CREATE TABLE `foro` (
  `Id_foro` int(11) NOT NULL,
  `Id_clase` int(11) NOT NULL,
  `Fecha_foro` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `horarios`
--

CREATE TABLE `horarios` (
  `id_horario` int(11) NOT NULL,
  `id_clase` int(11) NOT NULL,
  `dia_semana` varchar(10) NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `identidad`
--

CREATE TABLE `identidad` (
  `id_docu` int(11) NOT NULL,
  `docu` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `identidad`
--

INSERT INTO `identidad` (`id_docu`, `docu`) VALUES
(1, 'Cédula de ciudadanía'),
(2, 'Tarjeta de identidad');

-- --------------------------------------------------------

--
-- Table structure for table `materia`
--

CREATE TABLE `materia` (
  `Id_materia` int(11) NOT NULL,
  `Materia` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `materia`
--

INSERT INTO `materia` (`Id_materia`, `Materia`) VALUES
(0, 'Materia Predeterminada'),
(1, 'Matematicas'),
(2, 'Analisis de datos');

-- --------------------------------------------------------

--
-- Table structure for table `materias`
--

CREATE TABLE `materias` (
  `id_materia` int(11) NOT NULL,
  `Materia` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `materias_salones`
--

CREATE TABLE `materias_salones` (
  `id_materia_salon` int(11) NOT NULL,
  `id_materia` int(11) NOT NULL,
  `id_salon` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notas`
--

CREATE TABLE `notas` (
  `Id_nota` int(11) NOT NULL,
  `Id_tarea_user` int(11) NOT NULL,
  `Nota` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `respuestas`
--

CREATE TABLE `respuestas` (
  `Id_respuestas` int(11) NOT NULL,
  `Contenido` varchar(255) NOT NULL,
  `Fecha_resp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `respuesta_tarea`
--

CREATE TABLE `respuesta_tarea` (
  `Id_Respuesta` int(11) NOT NULL,
  `Contenido` varchar(255) DEFAULT NULL,
  `Archivo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `Id_rol` int(11) NOT NULL,
  `Tipo_rol` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`Id_rol`, `Tipo_rol`) VALUES
(1, 'Administrador'),
(2, 'Docente'),
(3, 'Estudiante');

-- --------------------------------------------------------

--
-- Table structure for table `salones`
--

CREATE TABLE `salones` (
  `id_salon` int(11) NOT NULL,
  `Nombre_salon` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tareas`
--

CREATE TABLE `tareas` (
  `Id_tarea` int(11) NOT NULL,
  `Titulo_tarea` varchar(255) NOT NULL,
  `Desc_tarea` varchar(255) DEFAULT NULL,
  `Archivo_tarea` varchar(255) DEFAULT NULL,
  `Fecha_entreg` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tareas_user`
--

CREATE TABLE `tareas_user` (
  `Id_tarea_user` int(11) NOT NULL,
  `Id_tarea` int(11) NOT NULL,
  `Id_tarea_estado` int(11) NOT NULL,
  `Id_Respuesta` int(11) DEFAULT NULL,
  `Fecha_subido` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tema_foro`
--

CREATE TABLE `tema_foro` (
  `Id_tema` int(11) NOT NULL,
  `Titulo` varchar(255) DEFAULT NULL,
  `Contenido` varchar(255) DEFAULT NULL,
  `Fecha_tema` datetime DEFAULT NULL,
  `Id_respuestas` int(11) NOT NULL,
  `Id_foro` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `Id_user` int(11) NOT NULL,
  `Nombres` varchar(255) NOT NULL,
  `Correo` varchar(255) NOT NULL,
  `Contrasena` varchar(255) NOT NULL,
  `Avatar` varchar(255) DEFAULT NULL,
  `Telefono` bigint(11) DEFAULT NULL,
  `Id_rol` int(11) NOT NULL,
  `Id_estado` int(11) NOT NULL,
  `id_docu` int(2) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `reset_token` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `reset_expira` datetime DEFAULT NULL,
  `id_ficha` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`Id_user`, `Nombres`, `Correo`, `Contrasena`, `Avatar`, `Telefono`, `Id_rol`, `Id_estado`, `id_docu`, `fecha_registro`, `reset_token`, `reset_expira`, `id_ficha`) VALUES
(123123, 'daasd', 'ewdaaw@dads.com', '$2y$12$75yY/DMLR7wg4nr9r9E0m.jaxIQnKHJYTQPp7BmHFhO1rppUihpr6', '', 231231424, 2, 1, 1, '2025-02-07 16:38:01', NULL, NULL, 0),
(11223344, 'Marta Ruiz', 'mruiz@mail.com', '$2y$12$yX416vghtG2aHCP7f0xNIej7cnsm7AwRrZlUVYxQcxtIKyVneoqH.', NULL, 3009876544, 3, 2, 2, '2025-03-23 18:06:59', NULL, NULL, 0),
(12345678, 'Laura Martínez', 'lauramartinez@mail.com', '$2y$12$kXTdgLZBHFg2ejdJ7hlu6uIrkoRGD6cWdvUPD34oupkIKrTRfFiiC', NULL, 3005678901, 3, 2, 2, '2025-03-23 20:49:48', NULL, NULL, 0),
(13133676, 'mary elizabet btamcourt', 'mary@gmail.com', '$2y$12$uZrd0Qq/Oqx7TKqDZlEuLu10q/NVSAdZ1Zsy10gbRl0trxjz4MhkO', NULL, 3028623064, 2, 1, 1, '2025-03-23 16:13:46', NULL, NULL, 0),
(22334455, 'Luis Fernández', 'luisfernandez@mail.com', '$2y$12$BiA3W3JXN244fEnNtvxcIOnVWEXimqCnEkb5JKDAPluv1p7wVVBSG', NULL, 3005432109, 3, 1, 1, '2025-03-23 20:49:48', NULL, NULL, 0),
(33445566, 'Sofia González', 'sofia@mail.com', '$2y$12$KhXN7JjBxXUQa94BiHDOyu0ztxxN4GKIy3kggqjZdMiOJxKesPcO2', NULL, 3006782345, 3, 1, 2, '2025-03-23 18:06:59', NULL, NULL, 0),
(38362368, 'mary elizabet', 'mary@gmaiil.com', '$2y$12$azR1X3Wyz63VjgQ9kpzV7.xjukJuoO6cHqomtTnii9syT4zzMOx72', '', 1234567890, 1, 1, 1, '2025-02-05 22:32:15', NULL, NULL, 0),
(44556677, 'Antonio Díaz', 'antonio.diaz@mail.com', '$2y$12$Wvn96WEP09Agm1oGKIxqFeN1jM7S4i0CBrRDeULrR3.iNjPDlHWD6', NULL, 3002345678, 3, 2, 2, '2025-03-23 20:49:49', NULL, NULL, 0),
(55667788, 'Carmen López', 'carmen.lopez@mail.com', '$2y$12$Y8Ru8tIDF8CJGSQT0DoJgOda7Kquyl6RU1Xoj/JmILm0zDz3g8Bta', NULL, 3003456789, 3, 2, 1, '2025-03-23 20:49:49', NULL, NULL, 0),
(65634846, 'Magdalena Lozano', 'magdys_2007@hotmail.com', '$2y$12$LV/Ztm1osVR07RKB9g3OpOp9DaPNyfvT42MYMk6tN9mtcC2OeZfci', NULL, 3133409045, 1, 1, 1, '2024-12-11 06:51:20', NULL, NULL, 0),
(65904846, 'Juan Pérez', 'juanperez@mail.com', '$2y$12$edwbUf6XRWiCBqgK0Fxx/u6f4CEPetqS5e.D2sTBo5kOq3KcWrAEu', NULL, 3001234567, 3, 1, 1, '2025-03-23 21:35:12', NULL, NULL, 0),
(65904847, 'Carlos López', 'carloslopez@mail.com', '$2y$12$MSW6ZMLYl1xcv4WeD9NeQuELmOp/0hMuVbRaEtLzzN8dWI.uLzH..', NULL, 3001122334, 2, 1, 1, '2025-03-23 21:35:12', NULL, NULL, 0),
(66778899, 'José Pérez', 'joseperez@mail.com', '$2y$12$Aq3XbDWnlCMr88Ak.sNV5e3p4qS5eJ/qF5swMnh7ySdSxjUBfHOWW', NULL, 3004567890, 2, 2, 2, '2025-03-23 20:49:49', NULL, NULL, 0),
(77889900, 'Elena García', 'elenagarcia@mail.com', '$2y$12$XjcqK2iPQNSHwt8Ar1V0He8hfw8Y9RcV4CuwOyVDBHaKvkF.6IB0e', NULL, 3005678901, 3, 1, 2, '2025-03-23 20:49:49', NULL, NULL, 0),
(88990011, 'Ricardo Martínez', 'ricardo@mail.com', '$2y$12$0ZuXJN/0IQAJIqSZypHV1.FwIWYg82qsjUeEGIl6iT4ak9zWlKZe.', NULL, 3006789012, 3, 1, 2, '2025-03-23 20:49:50', NULL, NULL, 0),
(98765432, 'Pedro Sánchez', 'pedrosanchez@mail.com', '$2y$12$1QnEbRmutEVzykF7Bw9dxOmFHcKk5IUls0g3SG5NueL0pS6.5Kz4m', NULL, 3006543210, 2, 2, 1, '2025-03-23 20:49:48', NULL, NULL, 0),
(99001122, 'Isabel Fernández', 'isabelfernandez@mail.com', '$2y$12$poloFTN2YbecQF/U.QvHc.49D/4cpX5cJCnaVgW3GPREomTVP1Lhe', NULL, 3007890123, 2, 1, 1, '2025-03-23 20:49:50', NULL, NULL, 0),
(1104940105, 'Edier Moyano', 'ediersmb@gmail.com', '$2y$12$c9viwsRfQqS.wUTwiOhJ7.4dxBnzA/hzqXvrZ6lwnuPo3cycgbghC', NULL, 312049123, 1, 1, 1, '2024-12-11 08:52:08', NULL, NULL, 0),
(1105676429, 'Nelson', 'nelson@gmail.com', '$2y$12$LjsoiTu8ZcQpGT1Vo68GM.RA.28lmA62SDOH.YFFM1cl/rWdOvH/e', NULL, 3123012030, 1, 1, 1, '2024-12-11 06:51:20', NULL, NULL, 0),
(1109492100, 'Ana García', 'anagarcia@mail.com', '$2y$12$LUcHSPRVXl8lcML09LTaBee3txcRqAoqd5/UZTdmPjoEyahgsArvC', NULL, 3009876543, 2, 1, 2, '2025-03-23 21:35:12', NULL, NULL, 0),
(1109492105, 'Juan Aranda', 'jsebaslozano2006@gmail.com', '$2y$12$wPVvO2tp/IGsD//jfxqaoexterGI0sW8ClRMULrrg9aaaonIv5UGy', NULL, 3187523586, 1, 1, 2, '2025-03-23 20:49:50', NULL, NULL, 0),
(1111111111, 'WickedJulian', 'Wickedjulian@gmail.com', '$2y$12$uBYsjxxBdj1eU9EzYYjTUOK0cZhWKdxzrmQNHhizT0lt1kSAdajQm', NULL, 312049123, 1, 1, 1, '2025-03-23 20:49:50', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `usuarios_clases`
--

CREATE TABLE `usuarios_clases` (
  `id_usuario_clase` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_clase` int(11) NOT NULL,
  `id_materia` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios_clases`
--

INSERT INTO `usuarios_clases` (`id_usuario_clase`, `id_user`, `id_clase`, `id_materia`) VALUES
(1, 99001122, 0, NULL),
(2, 123123, 0, NULL),
(3, 65904846, 0, NULL),
(4, 12345678, 0, NULL),
(5, 22334455, 0, NULL),
(6, 44556677, 0, NULL),
(7, 55667788, 0, NULL),
(8, 77889900, 0, NULL),
(9, 88990011, 0, NULL),
(11, 1109492100, 0, 2),
(12, 65904847, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `usuarios_fichas`
--

CREATE TABLE `usuarios_fichas` (
  `id_usuario_ficha` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_ficha` int(11) NOT NULL,
  `fecha_asignacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usuarios_roles`
--

CREATE TABLE `usuarios_roles` (
  `id_usuario_rol` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clases`
--
ALTER TABLE `clases`
  ADD PRIMARY KEY (`Id_clase`),
  ADD KEY `Id_tarea` (`Id_tarea`),
  ADD KEY `Id_materia` (`Id_materia`),
  ADD KEY `Id_user` (`Id_user`),
  ADD KEY `fk_clases_fichas` (`id_ficha`);

--
-- Indexes for table `clases_tareas`
--
ALTER TABLE `clases_tareas`
  ADD PRIMARY KEY (`id_clase_tarea`),
  ADD KEY `id_clase` (`id_clase`),
  ADD KEY `id_tarea` (`id_tarea`);

--
-- Indexes for table `compartido`
--
ALTER TABLE `compartido`
  ADD PRIMARY KEY (`Id_compartido`),
  ADD KEY `Id_user` (`Id_user`),
  ADD KEY `Id_doc` (`Id_doc`);

--
-- Indexes for table `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`Id_doc`);

--
-- Indexes for table `estado`
--
ALTER TABLE `estado`
  ADD PRIMARY KEY (`Id_estado`);

--
-- Indexes for table `estado_tarea`
--
ALTER TABLE `estado_tarea`
  ADD PRIMARY KEY (`Id_tarea_estado`);

--
-- Indexes for table `fichas`
--
ALTER TABLE `fichas`
  ADD PRIMARY KEY (`id_ficha`),
  ADD UNIQUE KEY `numero_ficha` (`numero_ficha`);

--
-- Indexes for table `foro`
--
ALTER TABLE `foro`
  ADD PRIMARY KEY (`Id_foro`),
  ADD KEY `Id_clase` (`Id_clase`);

--
-- Indexes for table `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `id_clase` (`id_clase`);

--
-- Indexes for table `identidad`
--
ALTER TABLE `identidad`
  ADD PRIMARY KEY (`id_docu`);

--
-- Indexes for table `materia`
--
ALTER TABLE `materia`
  ADD PRIMARY KEY (`Id_materia`);

--
-- Indexes for table `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`id_materia`);

--
-- Indexes for table `materias_salones`
--
ALTER TABLE `materias_salones`
  ADD PRIMARY KEY (`id_materia_salon`),
  ADD KEY `id_materia` (`id_materia`),
  ADD KEY `id_salon` (`id_salon`);

--
-- Indexes for table `notas`
--
ALTER TABLE `notas`
  ADD PRIMARY KEY (`Id_nota`),
  ADD KEY `Id_tarea_user` (`Id_tarea_user`);

--
-- Indexes for table `respuestas`
--
ALTER TABLE `respuestas`
  ADD PRIMARY KEY (`Id_respuestas`);

--
-- Indexes for table `respuesta_tarea`
--
ALTER TABLE `respuesta_tarea`
  ADD PRIMARY KEY (`Id_Respuesta`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`Id_rol`);

--
-- Indexes for table `salones`
--
ALTER TABLE `salones`
  ADD PRIMARY KEY (`id_salon`);

--
-- Indexes for table `tareas`
--
ALTER TABLE `tareas`
  ADD PRIMARY KEY (`Id_tarea`);

--
-- Indexes for table `tareas_user`
--
ALTER TABLE `tareas_user`
  ADD PRIMARY KEY (`Id_tarea_user`),
  ADD KEY `Id_tarea` (`Id_tarea`),
  ADD KEY `Id_tarea_estado` (`Id_tarea_estado`),
  ADD KEY `Id_Respuesta` (`Id_Respuesta`);

--
-- Indexes for table `tema_foro`
--
ALTER TABLE `tema_foro`
  ADD PRIMARY KEY (`Id_tema`),
  ADD KEY `Id_respuestas` (`Id_respuestas`),
  ADD KEY `Id_foro` (`Id_foro`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`Id_user`),
  ADD KEY `Id_rol` (`Id_rol`),
  ADD KEY `Id_estado` (`Id_estado`),
  ADD KEY `id_docu` (`id_docu`);

--
-- Indexes for table `usuarios_clases`
--
ALTER TABLE `usuarios_clases`
  ADD PRIMARY KEY (`id_usuario_clase`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_clase` (`id_clase`),
  ADD KEY `fk_usuarios_clases_materias` (`id_materia`);

--
-- Indexes for table `usuarios_fichas`
--
ALTER TABLE `usuarios_fichas`
  ADD PRIMARY KEY (`id_usuario_ficha`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_ficha` (`id_ficha`);

--
-- Indexes for table `usuarios_roles`
--
ALTER TABLE `usuarios_roles`
  ADD PRIMARY KEY (`id_usuario_rol`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clases_tareas`
--
ALTER TABLE `clases_tareas`
  MODIFY `id_clase_tarea` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `estado`
--
ALTER TABLE `estado`
  MODIFY `Id_estado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `fichas`
--
ALTER TABLE `fichas`
  MODIFY `id_ficha` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `identidad`
--
ALTER TABLE `identidad`
  MODIFY `id_docu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `materias`
--
ALTER TABLE `materias`
  MODIFY `id_materia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `materias_salones`
--
ALTER TABLE `materias_salones`
  MODIFY `id_materia_salon` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `Id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `salones`
--
ALTER TABLE `salones`
  MODIFY `id_salon` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usuarios_clases`
--
ALTER TABLE `usuarios_clases`
  MODIFY `id_usuario_clase` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `usuarios_fichas`
--
ALTER TABLE `usuarios_fichas`
  MODIFY `id_usuario_ficha` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usuarios_roles`
--
ALTER TABLE `usuarios_roles`
  MODIFY `id_usuario_rol` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `clases`
--
ALTER TABLE `clases`
  ADD CONSTRAINT `clases_ibfk_1` FOREIGN KEY (`Id_tarea`) REFERENCES `tareas` (`Id_tarea`),
  ADD CONSTRAINT `clases_ibfk_2` FOREIGN KEY (`Id_materia`) REFERENCES `materia` (`Id_materia`),
  ADD CONSTRAINT `clases_ibfk_3` FOREIGN KEY (`Id_user`) REFERENCES `usuarios` (`Id_user`),
  ADD CONSTRAINT `fk_clases_fichas` FOREIGN KEY (`id_ficha`) REFERENCES `fichas` (`id_ficha`);

--
-- Constraints for table `clases_tareas`
--
ALTER TABLE `clases_tareas`
  ADD CONSTRAINT `clases_tareas_ibfk_1` FOREIGN KEY (`id_clase`) REFERENCES `clases` (`Id_clase`),
  ADD CONSTRAINT `clases_tareas_ibfk_2` FOREIGN KEY (`id_tarea`) REFERENCES `tareas` (`Id_tarea`);

--
-- Constraints for table `compartido`
--
ALTER TABLE `compartido`
  ADD CONSTRAINT `compartido_ibfk_1` FOREIGN KEY (`Id_user`) REFERENCES `usuarios` (`Id_user`),
  ADD CONSTRAINT `compartido_ibfk_2` FOREIGN KEY (`Id_doc`) REFERENCES `documentos` (`Id_doc`);

--
-- Constraints for table `foro`
--
ALTER TABLE `foro`
  ADD CONSTRAINT `foro_ibfk_1` FOREIGN KEY (`Id_clase`) REFERENCES `clases` (`Id_clase`);

--
-- Constraints for table `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`id_clase`) REFERENCES `clases` (`Id_clase`);

--
-- Constraints for table `materias_salones`
--
ALTER TABLE `materias_salones`
  ADD CONSTRAINT `materias_salones_ibfk_1` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`),
  ADD CONSTRAINT `materias_salones_ibfk_2` FOREIGN KEY (`id_salon`) REFERENCES `salones` (`id_salon`);

--
-- Constraints for table `notas`
--
ALTER TABLE `notas`
  ADD CONSTRAINT `notas_ibfk_1` FOREIGN KEY (`Id_tarea_user`) REFERENCES `tareas_user` (`Id_tarea_user`);

--
-- Constraints for table `tareas_user`
--
ALTER TABLE `tareas_user`
  ADD CONSTRAINT `tareas_user_ibfk_1` FOREIGN KEY (`Id_tarea`) REFERENCES `tareas` (`Id_tarea`),
  ADD CONSTRAINT `tareas_user_ibfk_2` FOREIGN KEY (`Id_tarea_estado`) REFERENCES `estado_tarea` (`Id_tarea_estado`),
  ADD CONSTRAINT `tareas_user_ibfk_3` FOREIGN KEY (`Id_Respuesta`) REFERENCES `respuesta_tarea` (`Id_Respuesta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tema_foro`
--
ALTER TABLE `tema_foro`
  ADD CONSTRAINT `tema_foro_ibfk_1` FOREIGN KEY (`Id_respuestas`) REFERENCES `respuestas` (`Id_respuestas`),
  ADD CONSTRAINT `tema_foro_ibfk_2` FOREIGN KEY (`Id_foro`) REFERENCES `foro` (`Id_foro`);

--
-- Constraints for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`Id_rol`) REFERENCES `roles` (`Id_rol`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`Id_estado`) REFERENCES `estado` (`Id_estado`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usuarios_ibfk_3` FOREIGN KEY (`id_docu`) REFERENCES `identidad` (`id_docu`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `usuarios_clases`
--
ALTER TABLE `usuarios_clases`
  ADD CONSTRAINT `fk_usuarios_clases_materias` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`Id_materia`),
  ADD CONSTRAINT `usuarios_clases_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `usuarios` (`Id_user`),
  ADD CONSTRAINT `usuarios_clases_ibfk_2` FOREIGN KEY (`id_clase`) REFERENCES `clases` (`Id_clase`);

--
-- Constraints for table `usuarios_fichas`
--
ALTER TABLE `usuarios_fichas`
  ADD CONSTRAINT `usuarios_fichas_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `usuarios` (`Id_user`),
  ADD CONSTRAINT `usuarios_fichas_ibfk_2` FOREIGN KEY (`id_ficha`) REFERENCES `fichas` (`id_ficha`);

--
-- Constraints for table `usuarios_roles`
--
ALTER TABLE `usuarios_roles`
  ADD CONSTRAINT `usuarios_roles_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `usuarios` (`Id_user`),
  ADD CONSTRAINT `usuarios_roles_ibfk_2` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`Id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
