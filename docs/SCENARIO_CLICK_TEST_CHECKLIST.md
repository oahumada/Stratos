# Checklist de pruebas - Scenario Planning (clics/niveles)

Objetivo: verificar comportamiento de nodos por niveles y la ausencia de animaciones en clics sobre nodos de nivel 2.

Pasos manuales:

- Abrir la página de Scenario Planning y abrir DevTools → Console.
- Activar debug opcional: `window.__DEBUG__ = true` en la consola.
- Reproducir clic en un nodo de nivel 1 (capacidad):
  - Verificar que las animaciones ocurren y que el log muestra `level=1`.
- Reproducir clic en una competencia (nivel 2):
  - Verificar que no hay animaciones visibles (transiciones suprimidas).
  - Verificar en la consola que aparece `node.click.level2` y que no se producen llamadas adicionales.
- Probar doble clic rápido en el mismo nodo: asegurarse que el segundo clic es ignorado (debounce).
- Probar "Restaurar vista" (si existe): comprobar que los nodos vuelven a su estado normal (visibles y sin flags).

Notas:

- Desactivar debug con `window.__DEBUG__ = false`.
