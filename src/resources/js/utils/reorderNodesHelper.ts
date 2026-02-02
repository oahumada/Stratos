export interface NodeItem {
  id: number | string;
  x?: number;
  y?: number;
  [k: string]: any;
}

function clampY(val: number, min = 40, max = 2000) {
  return Math.max(min, Math.min(max, val));
}

/**
 * Compute reordered nodes positions following the same rules as the UI `reorderNodes`
 * Returns a new array of nodes with `x` and `y` set.
 */
export function reorderNodesHelper(nodes: NodeItem[], width: number, height: number, scenarioNode?: NodeItem) {
  // Defensive: if the scenario node was accidentally included in the nodes list,
  // exclude it from layout computation and re-insert unchanged at the end.
  let scenarioIncluded = false;
  const scenarioId = scenarioNode?.id;
  let workingNodes = nodes.slice();
  if (scenarioId != null && workingNodes.some((n) => n && n.id === scenarioId)) {
    scenarioIncluded = true;
    workingNodes = workingNodes.filter((n) => n.id !== scenarioId);
  }

  const total = workingNodes.length;
  if (!total) return nodes.slice();
  const cx = Math.round(width / 2);
  const cy = Math.round(height / 2 - 30);
  const marginH = 48;
  const availableW = Math.max(200, width - marginH * 2);

  let newNodes = workingNodes.map((n) => ({ ...n }));

  if (total === 1) {
    newNodes = newNodes.map((n) => ({ ...n, x: cx, y: cy }));
  } else if (total === 2) {
    const gap = Math.min(220, Math.floor(availableW / 3));
    newNodes = newNodes.map((n, i) => ({ ...n, x: cx + (i === 0 ? -gap / 2 : gap / 2), y: cy }));
  } else if (total === 3) {
    const gap = Math.min(220, Math.floor(availableW / 4));
    newNodes = newNodes.map((n, i) => {
      if (i === 1) return { ...n, x: cx, y: cy };
      return { ...n, x: cx + (i === 0 ? -gap : gap), y: cy };
    });
  } else if (total >= 4 && total <= 6) {
    const cols = total;
    const spacing = Math.min(160, Math.floor(availableW / cols));
    const totalW = (cols - 1) * spacing;
    const startX = Math.round(cx - totalW / 2);
    newNodes = newNodes.map((n, i) => ({ ...n, x: startX + i * spacing, y: cy }));
  } else {
    // grid layout for larger sets: choose a near-square grid (ceil(sqrt(total))) up to 6 columns
    // Special-case: prefer a 2x5 layout for exactly 10 items as requested
    let cols = Math.min(6, Math.max(2, Math.ceil(Math.sqrt(total))));
    let rows = Math.ceil(total / cols);
    if (total === 10) {
      cols = 5;
      rows = 2;
    }
    const spacingX = Math.min(140, Math.floor(availableW / cols));
    const spacingY = Math.min(140, Math.floor((height - 160) / Math.max(1, rows)));
    const totalGridW = (cols - 1) * spacingX;
    const totalGridH = (rows - 1) * spacingY;
    const startX = Math.round(cx - totalGridW / 2);
    const downwardBias = 40;
    const startY = Math.round(cy - totalGridH / 2 + downwardBias);
    newNodes = newNodes.map((n, i) => {
      const r = Math.floor(i / cols);
      const c = i % cols;
      return { ...n, x: startX + c * spacingX, y: clampY(startY + r * spacingY) } as NodeItem;
    });
  }

  // origin separation shift (to avoid overlapping scenario origin)
  try {
    const MIN_ORIGIN_SEPARATION = 120;
    if (scenarioNode) {
      const sx = scenarioNode.x ?? Math.round(width / 2);
      const sy = scenarioNode.y ?? Math.round(height * 0.12);
      let requiredShift = 0;
      newNodes.forEach((n) => {
        if (!n || n.x == null || n.y == null) return;
        const dx = n.x - sx;
        const dy = n.y - sy;
        const dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < MIN_ORIGIN_SEPARATION) {
          const need = MIN_ORIGIN_SEPARATION - dist;
          if (need > requiredShift) requiredShift = need;
        }
      });
      if (requiredShift > 0) {
        const margin = 20;
        const shift = Math.round(requiredShift + margin);
        newNodes = newNodes.map((n) => {
          if (!n || n.y == null) return n;
          return { ...n, y: clampY((n.y ?? 0) + shift) } as NodeItem;
        });
      }
    }
  } catch (err) {
    void err;
  }

  // If scenario node was removed earlier, reattach it (unchanged) at the end
  if (scenarioIncluded && scenarioNode) {
    return [...newNodes, { ...scenarioNode } as NodeItem];
  }

  return newNodes;
}

export default reorderNodesHelper;
