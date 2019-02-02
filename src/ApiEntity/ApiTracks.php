<?php

namespace App\ApiEntity;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class ApiTracks
{
    public $tracks;

    public function __construct(array $trackArray)
    {
        $this->tracks = $trackArray;
    }

    public function addTrack(ApiTrack $track)
    {
        $this->tracks[] = $track;
    }

    public function getTags()
    {
        $tags = [];
        $flattenedTags = [];
        foreach ($this->tracks as $track) {
            if (isset($track->tags)) {
                $tags[] = $track->tags;
            }
        }

        // https://stackoverflow.com/questions/1319903/how-to-flatten-a-multidimensional-array
        $tagsIterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($tags));
        foreach ($tagsIterator as $t) {
            $flattenedTags[] = $t;
        }

        return array_values(array_unique($flattenedTags));
    }
}
