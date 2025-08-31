# Marikaditas Literarias Website

Sitio web para un emprendimiento de cajas de suscripción literaria.

<img src="./images/screen.png"  />

## Features

- Diseño moderno y responsive.
- Navbar fija que cambia de color al hacer scroll.
- Modales de Bootstrap para detalles de producto y carrito de compras.
- Carrito de compras funcional con JavaScript.

## Usage

Este sitio está construido con [Bootstrap](https://getbootstrap.com/) y [Sass](https://sass-lang.com/). Utiliza [Font Awesome](https://fontawesome.com/) para los íconos.

Para personalizar este sitio, necesitas tener [Node.js](https://nodejs.org/en/) instalado. Luego, clona este repositorio y en la carpeta del proyecto ejecuta:

```bash
npm install
```

Esto instalará Bootstrap, Sass y Font Awesome. Para compilar tus archivos CSS desde Sass, ejecuta:

```bash
npm run sass:build
```

Para observar los cambios en tus archivos Sass y compilarlos automáticamente, ejecuta:

```bash
npm run sass:watch
```

Puedes añadir variables de Bootstrap en el archivo `scss/bootstrap.scss`. Para tus propios estilos, usa el archivo `scss/styles.scss`.
