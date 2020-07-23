<?php

namespace App\Traits;

trait Dijkstra
{
    public $INT_MAX = PHP_FLOAT_MAX;

    public function MinimumDistance($distance, $shortestPathTreeSet, $verticesCount)
    {
        $min = $this->INT_MAX;
        $minIndex = 0;

        for ($v = 0; $v < $verticesCount; ++$v) {
            if ($shortestPathTreeSet[$v] == false && $distance[$v] <= $min) {
                $min = $distance[$v];
                $minIndex = $v;
            }
        }

        return $minIndex;
    }

    public function PrintResult($distance, $verticesCount)
    {
        echo "<pre>" . "Vertex    Distance from source" . "</pre>";

        for ($i = 0; $i < $verticesCount; ++$i)
            echo "<pre>" . $i . "\t  " . $distance[$i] . "</pre>";
    }

    public function findShortestPath($graph, $source, $verticesCount)
    {
        $INT_MAX = $this->INT_MAX;
        $distance = array();
        $shortestPathTreeSet = array();

        for ($i = 0; $i < $verticesCount; ++$i) {
            $distance[$i] = $INT_MAX;
            $shortestPathTreeSet[$i] = false;
        }

        $distance[$source] = 0;

        for ($count = 0; $count < $verticesCount - 1; ++$count) {
            $u = $this->MinimumDistance($distance, $shortestPathTreeSet, $verticesCount);
            $shortestPathTreeSet[$u] = true;

            for ($v = 0; $v < $verticesCount; ++$v)
                if (!$shortestPathTreeSet[$v] && $graph[$u][$v] && $distance[$u] != $INT_MAX && $distance[$u] + $graph[$u][$v] < $distance[$v])
                    $distance[$v] = $distance[$u] + $graph[$u][$v];
        }

        $this->PrintResult($distance, $verticesCount);
    }
}
