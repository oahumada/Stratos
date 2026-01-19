import { ref, computed } from 'vue'
import * as d3 from 'd3'

/**
 * Composable para manejar la fisica del Cerebro Stratos
 * Inspirado en TheBrain - Navegacion organica de capacidades
 */
export function useBrainPhysics(initialNodes = [], initialLinks = []) {
  const nodes = ref(initialNodes)
  const links = ref(initialLinks)
  const simulation = ref(null)
  const focusedNodeId = ref(null)

  const width = ref(window.innerWidth)
  const height = ref(window.innerHeight - 73)

  /**
   * Inicializa la simulacion de fisica D3
   */
  const initSimulation = (customWidth = null, customHeight = null) => {
    if (customWidth) width.value = customWidth
    if (customHeight) height.value = customHeight

    simulation.value = d3.forceSimulation(nodes.value)
      .force('link', d3.forceLink(links.value)
        .id(d => d.id)
        .distance(150)
      )
      .force('charge', d3.forceManyBody().strength(-400))
      .force('center', d3.forceCenter(width.value / 2, height.value / 2))
      .force('collision', d3.forceCollide().radius(70))
      .alphaDecay(0.02)
      .on('tick', () => {
        nodes.value = [...nodes.value]
      })
  }

  /**
   * Enfoca un nodo (comportamiento TheBrain)
   */
  const focusNode = (nodeId) => {
    focusedNodeId.value = nodeId
    const node = nodes.value.find(n => n.id === nodeId)
    if (!node) return

    simulation.value.alphaTarget(0.3).restart()
    node.fx = width.value / 2
    node.fy = height.value / 2

    setTimeout(() => {
      node.fx = null
      node.fy = null
      simulation.value.alphaTarget(0)
    }, 2000)
  }

  /**
   * Recalcula la fisica al cambiar de escenario
   */
  const recalculate = (newNodes, newLinks) => {
    nodes.value = newNodes
    links.value = newLinks
    simulation.value.nodes(nodes.value)
    simulation.value.force('link').links(links.value)
    simulation.value.alpha(1).restart()
  }

  /**
   * Calcula el peso gravitacional de un nodo
   * Nodos mas importantes atraen mas
   */
  const getNodeGravity = (node) => {
    return -200 - (node.importance * 100)
  }

  /**
   * Cambia la intensidad de repulsion entre nodos
   */
  const setRepulsionStrength = (strength) => {
    if (simulation.value) {
      simulation.value.force('charge').strength(strength)
      simulation.value.alpha(0.3).restart()
    }
  }

  return {
    nodes,
    links,
    simulation,
    focusedNodeId,
    width,
    height,
    initSimulation,
    focusNode,
    recalculate,
    getNodeGravity,
    setRepulsionStrength
  }
}
