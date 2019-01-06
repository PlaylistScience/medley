<?php

namespace App\ApiEntity;

use App\ApiEntity\Track;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class Tracks {
    var $tracks;

    public function __construct(Array $trackArray)
    {
        $this->tracks = $trackArray;
    }

    public function addTrack(Track $track)
    {
        $this->tracks[] = $track;
    }

    public function getTags()
    {
        $tags = [];
        $flattenedTags = [];
        foreach ($this->tracks as $track) {
            if (isset($track->tags)) $tags[] = $track->tags;
        }

        // https://stackoverflow.com/questions/1319903/how-to-flatten-a-multidimensional-array
        $tagsIterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($tags));
        foreach($tagsIterator as $t) {
            $flattenedTags[] = $t;
        }
        return $flattenedTags;
        return array_unique($flattenedTags);
    }
}
