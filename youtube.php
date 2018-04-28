class youtube {
    
    private $channel;
    private $api;
    private $maxResults = 10;
    
    function __construct($channel, $api, $maxResults = null) {
        
        if (!empty($channel) && $channel != null) {
            
            $this->channel = $channel;
        }
        
        if (!empty($api) && $api != null) {
            
            $this->api = $api;
        }
        
        if (!empty($maxResults) && $maxResults != null) {
            
            $this->maxResults = $maxResults;
        }
    }
    
    private function request($NextPagetoken = null) {
        
        if ($NextPagetoken != null) {
            
            return json_decode(
                    file_get_contents(
                        'https://www.googleapis.com/youtube/v3/search?order=date&pageToken=' . $NextPagetoken . '&part=snippet&channelId=' . $this->channel . '&maxResults=' . $this->maxResults . '&key=' . $this->api
                        )
                    , true);
        } else {
            
            return json_decode(
                    file_get_contents(
                        'https://www.googleapis.com/youtube/v3/search?order=date&part=snippet&channelId=' . $this->channel . '&maxResults=' . $this->maxResults . '&key=' . $this->api
                        )
                    , true);
        }
    }

    function get($NextPagetoken = null) {
        
        if ($NextPagetoken != null) {
            
            $videos = $this->request($NextPagetoken);
        } else {
            
            $videos = $this->request();
        }
        
        $i = 0;
        $return = array();
        
        if (isset($videos["nextPageToken"]) && !empty($videos["nextPageToken"])) {
            
            $return["NextPageToken"] = $videos["nextPageToken"];
        }
                
        foreach ($videos["items"] as $video) {
            
            if ($video["id"]["kind"] == "youtube#playlist") {
                
                continue;
            }
            
            $return["videos"][$i]["id"] = $video["id"]["videoId"];
            $return["videos"][$i]["title"] = $video["snippet"]["title"];
            $return["videos"][$i]["description"] = $video["snippet"]["description"];
            $return["videos"][$i]["timestamp"] = $video["snippet"]["publishedAt"];
            $return["videos"][$i]["channel"] = $video["snippet"]["channelTitle"];
            $return["videos"][$i]["thumb"] = $video["snippet"]["thumbnails"]["high"]["url"];
            $i++;
        }
        
        return $return;
    }
}
