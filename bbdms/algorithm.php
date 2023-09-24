<?php

class DijkstraShortestPath {

    private $graph = array();

    public function addEdge($source, $destination) {
        $sourceKey = $this->getKeyFromPoint($source);
        $destinationKey = $this->getKeyFromPoint($destination);

        if (!array_key_exists($sourceKey, $this->graph)) {
            $this->graph[$sourceKey] = array();
        }
        if (!array_key_exists($destinationKey, $this->graph)) {
            $this->graph[$destinationKey] = array();
        }
        array_push($this->graph[$sourceKey], $destinationKey);
        array_push($this->graph[$destinationKey], $sourceKey);
    }

    public function findShortestPath($source, $destination) {
        $distances = array();
        $parentNodes = array();

        foreach ($this->graph as $node => $neighbors) {
            $distances[$node] = INF;
        }

        $distances[$this->getKeyFromPoint($source)] = 0.0;

        $priorityQueue = new SplPriorityQueue();
        $priorityQueue->insert($this->getKeyFromPoint($source), 0.0);

        while (!$priorityQueue->isEmpty()) {
            $current = $priorityQueue->extract();
            if ($current === $this->getKeyFromPoint($destination)) {
                break;
            }

            foreach ($this->graph[$current] as $neighbor) {
                $distance = $distances[$current] + $this->haversineDistance($this->getPointFromKey($current), $this->getPointFromKey($neighbor));
                if ($distance < $distances[$neighbor]) {
                    $distances[$neighbor] = $distance;
                    $parentNodes[$neighbor] = $current;
                    $priorityQueue->insert($neighbor, -$distance);
                }
            }
        }

        $path = array();
        $current = $this->getKeyFromPoint($destination);
        while (isset($current)) {
            array_unshift($path, $this->getPointFromKey($current));
            $current = $parentNodes[$current] ?? null;
        }
        return $path;
    }

    private function haversineDistance($p1, $p2) {
        // ... (same haversine distance function)
    }

    private function getKeyFromPoint($point) {
        return $point['latitude'] . '_' . $point['longitude'];
    }

    private function getPointFromKey($key) {
        list($latitude, $longitude) = explode('_', $key);
        return ['latitude' => $latitude, 'longitude' => $longitude];
    }
}

// Example usage
$dijkstra = new DijkstraShortestPath();

$dijkstra->addEdge(["latitude" => 0, "longitude" => 0], ["latitude" => 1, "longitude" => 1]);
$dijkstra->addEdge(["latitude" => 1, "longitude" => 1], ["latitude" => 2, "longitude" => 2]);

$path = $dijkstra->findShortestPath(["latitude" => 0, "longitude" => 0], ["latitude" => 2, "longitude" => 2]);
print_r($path);

?>
