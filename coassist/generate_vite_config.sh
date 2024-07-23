#!/bin/bash

# Crear el archivo de configuración
config_file="vite.config.js"

# Generar el contenido del archivo de configuración
echo "import { defineConfig } from 'vite';" > $config_file
echo "" >> $config_file
echo "export default defineConfig({" >> $config_file
echo "  root: '.', // Define la raíz del proyecto" >> $config_file
echo "  build: {" >> $config_file
echo "    outDir: 'dist', // Directorio de salida para la construcción" >> $config_file
echo "    rollupOptions: {" >> $config_file
echo "      input: {" >> $config_file

# Agregar cada archivo HTML al objeto de entrada
for file in *.html; do
  # Obtener el nombre del archivo sin la extensión
  name=$(basename "$file" .html)
  # Agregar al archivo de configuración
  echo "        $name: './$file'," >> $config_file
done

# Finalizar el archivo de configuración
echo "      }" >> $config_file
echo "    }" >> $config_file
echo "  }," >> $config_file
echo "  server: {" >> $config_file
echo "    open: true, // Abre el navegador automáticamente" >> $config_file
echo "  }" >> $config_file
echo "});" >> $config_file

echo "Configuración de Vite generada en $config_file"

