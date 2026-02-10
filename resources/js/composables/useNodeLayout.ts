/**
 * Shared layout operations for hierarchical node visualization
 * Centralizes common patterns for positioning capabilities, competencies, and skills
 */

interface Position {
    x: number;
    y: number;
}

interface NodeItem {
    id: string | number;
    x?: number;
    y?: number;
    [key: string]: any;
}

interface Edge {
    source: string | number;
    target: string | number;
    [key: string]: any;
}

export function useNodeLayout() {
    /**
     * Find parent node from edges
     */
    function findParent(nodeId: string | number, edges: Edge[]): string | number | null {
        const parentEdge = edges.find((e: Edge) => e.target === nodeId);
        return parentEdge ? parentEdge.source : null;
    }

    /**
     * Find all children of a node
     */
    function findChildren(nodeId: string | number, edges: Edge[]): Edge[] {
        return edges.filter((e: Edge) => e.source === nodeId);
    }

    /**
     * Calculate center position between multiple points
     */
    function calculateCenter(points: Position[]): Position {
        if (points.length === 0) {
            return { x: 0, y: 0 };
        }

        const sum = points.reduce(
            (acc, p) => ({ x: acc.x + p.x, y: acc.y + p.y }),
            { x: 0, y: 0 }
        );

        return {
            x: sum.x / points.length,
            y: sum.y / points.length
        };
    }

    /**
     * Distribute nodes evenly in a circle around a center point
     */
    function distributeInCircle(
        center: Position,
        count: number,
        radius: number,
        startAngle: number = 0
    ): Position[] {
        const positions: Position[] = [];
        const angleStep = (2 * Math.PI) / count;

        for (let i = 0; i < count; i++) {
            const angle = startAngle + i * angleStep;
            positions.push({
                x: center.x + radius * Math.cos(angle),
                y: center.y + radius * Math.sin(angle)
            });
        }

        return positions;
    }

    /**
     * Distribute nodes in a grid layout
     */
    function distributeInGrid(
        startPosition: Position,
        count: number,
        options: {
            columns?: number;
            rowSpacing?: number;
            columnSpacing?: number;
        } = {}
    ): Position[] {
        const {
            columns = Math.ceil(Math.sqrt(count)),
            rowSpacing = 150,
            columnSpacing = 200
        } = options;

        const positions: Position[] = [];

        for (let i = 0; i < count; i++) {
            const row = Math.floor(i / columns);
            const col = i % columns;

            positions.push({
                x: startPosition.x + col * columnSpacing,
                y: startPosition.y + row * rowSpacing
            });
        }

        return positions;
    }

    /**
     * Distribute nodes horizontally
     */
    function distributeHorizontally(
        startPosition: Position,
        count: number,
        spacing: number = 200
    ): Position[] {
        const positions: Position[] = [];

        for (let i = 0; i < count; i++) {
            positions.push({
                x: startPosition.x + i * spacing,
                y: startPosition.y
            });
        }

        return positions;
    }

    /**
     * Distribute nodes vertically
     */
    function distributeVertically(
        startPosition: Position,
        count: number,
        spacing: number = 150
    ): Position[] {
        const positions: Position[] = [];

        for (let i = 0; i < count; i++) {
            positions.push({
                x: startPosition.x,
                y: startPosition.y + i * spacing
            });
        }

        return positions;
    }

    /**
     * Check if a position is within viewport bounds
     */
    function isInViewport(
        position: Position,
        viewport: { width: number; height: number; minX?: number; minY?: number }
    ): boolean {
        const minX = viewport.minX ?? 0;
        const minY = viewport.minY ?? 0;

        return (
            position.x >= minX &&
            position.x <= minX + viewport.width &&
            position.y >= minY &&
            position.y <= minY + viewport.height
        );
    }

    /**
     * Find nearest available position to avoid overlaps
     */
    function findNearestAvailablePosition(
        desiredPosition: Position,
        existingNodes: NodeItem[],
        minDistance: number = 100
    ): Position {
        let position = { ...desiredPosition };
        let attempts = 0;
        const maxAttempts = 50;

        while (attempts < maxAttempts) {
            const hasOverlap = existingNodes.some(node => {
                if (node.x === undefined || node.y === undefined) return false;
                
                const dx = position.x - node.x;
                const dy = position.y - node.y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                
                return distance < minDistance;
            });

            if (!hasOverlap) {
                return position;
            }

            // Try spiraling outward
            const angle = (attempts / maxAttempts) * 2 * Math.PI;
            const radius = minDistance * (1 + attempts / 10);
            
            position = {
                x: desiredPosition.x + radius * Math.cos(angle),
                y: desiredPosition.y + radius * Math.sin(angle)
            };

            attempts++;
        }

        return position;
    }

    return {
        findParent,
        findChildren,
        calculateCenter,
        distributeInCircle,
        distributeInGrid,
        distributeHorizontally,
        distributeVertically,
        isInViewport,
        findNearestAvailablePosition
    };
}

export default useNodeLayout;
