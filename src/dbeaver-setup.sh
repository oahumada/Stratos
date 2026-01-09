#!/bin/bash

# Script para conectar DBeaver a la BD de TalentIA
# Crea una conexión SQLite en DBeaver

DB_PATH="/home/omar/TalentIA/src/database/database.sqlite"

# Abrir DBeaver y crear conexión
echo "✅ DBeaver se está iniciando..."
echo ""
echo "📝 PASOS PARA CONFIGURAR LA CONEXIÓN A SQLite:"
echo ""
echo "1. En DBeaver, ve a: File → New Database Connection"
echo "2. Selecciona: SQLite"
echo "3. En 'Database file', coloca esta ruta:"
echo "   $DB_PATH"
echo "4. Haz clic en 'Finish'"
echo "5. Expande la conexión y haz clic derecho en la BD"
echo "6. Selecciona: ER Diagram → Show Diagram"
echo ""
echo "📊 O ejecuta este comando para abrir la BD directamente:"
echo "   dbeaver $DB_PATH &"
echo ""
