<?php

namespace App\ApiEntity;

class ApiTrack {
    var $title;
    var $url;
    var $created_at;
    var $tags;

    public function __construct(Array $data)
    {
        $this->title = $data['title'];
        $this->url = $data['url'];
        $this->created_at = $data['createdAt'];
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedAt($created_at): String
    {
        $this->created_at = $created_at;
    }

    public function getUrl(): String
    {
        return $url;
    }

    public function setUrl(String $url)
    {
        $this->url = $url;
    }

    public function getName(): String
    {
        return $this->name;
    }

    public function setName(String $name)
    {
        $this->name = $name;
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
