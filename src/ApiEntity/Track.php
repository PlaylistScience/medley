<?php

namespace App\ApiEntity;

class Track {
    var $title;
    var $url;
    var $createdAt;
    var $tags;

    public function __construct(Array $data)
    {
        $this->title = $data['title'];
        $this->url = $data['url'];
        $this->createdAt = $data['createdAt'];
    }

    public function hasTags(String $key)
    {
        return (boolean) $this->tags;
    }

    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    public function getTags()
    {
        return $this->tags;
    }
}
