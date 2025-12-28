#!/bin/bash

# 🚀 Setup Día 6 - Compilar y servir frontend

cd /workspaces/talentia/src

echo "📦 Installing dependencies..."
npm install

echo "🛠️ Building frontend..."
npm run build

echo "🚀 Starting Laravel server..."
php artisan serve --port=8000 &

echo ""
echo "✅ Frontend compilado"
echo "✅ Servidor corriendo en http://127.0.0.1:8000"
echo ""
echo "Para desarrollar en modo watch:"
echo "  npm run dev"
echo ""
echo "Próximos pasos:"
echo "1. Abre otro terminal y ejecuta: npm run dev"
echo "2. Abre http://127.0.0.1:8000 en el navegador"
echo "3. Inicia sesión con email/password"
echo "4. Navega a /people para ver la lista"
