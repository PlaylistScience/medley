<template>
    <div>
        <!-- The part that the youtube iframe api hooks into -->
        <div id="player"></div>

    </div>
</template>

<script lang="ts">
    export default {
        data: function () {
            return {
                player: null,
                // done: false,
            }
        },
        methods: {
            onYouTubeIframeAPIReady() {
                console.log('onYouTubeIframeAPIReady() vue function called');

                this.player = new YT.Player('player', {
                    height: '390',
                    width: '640',
                    videoId: 'M7lc1UVf-VE',
                    events: {
                    'onReady': onPlayerReady,
                    'onStateChange': onPlayerStateChange
                    }
                });
            },
            onPlayerReady(event) {
                console.log('onPlayerReady function');
                event.target.playVideo();
            },
            onPlayerStateChange() {
                console.log('onPlayerStateChange function');
            },
            stopVideo() {
                console.log('stopVideo function');
            }
        },
        created: function() {
        //     this.loadYoutube();

        //     // map expected youtube function names to vue names
        //     function onPlayerReady() {
        //         console.log('onPlayerReady function called by youtube api');
        //     }

        //     // function onYouTubeIframeAPIReady() {
        //     //     console.log('youtube ready function called');
        //     //     this.onYouTubeIframeAPIReady();
        //     // }

            var tag = document.createElement('script');

            tag.src = "https://www.youtube.com/iframe_api";
            var firstScriptTag = document.getElementsByTagName('script')[0];
            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

            // 3. This function creates an <iframe> (and YouTube player)
            //    after the API code downloads.
            var player;

            window.onYouTubeIframeAPIReady = this.onYouTubeIframeAPIReady;

            // window.onYouTubeIframeAPIReady = function() {
            //     player = new YT.Player('player', {
            //         height: '390',
            //         width: '640',
            //         videoId: 'M7lc1UVf-VE',
            //         events: {
            //         'onReady': onPlayerReady,
            //         'onStateChange': onPlayerStateChange
            //         }
            //     });
            // }

            // 4. The API will call this function when the video player is ready.
            window.onPlayerReady = this.onPlayerReady;
            // window.onPlayerReady = function(event) {
            //     event.target.playVideo();
            // }

            // 5. The API calls this function when the player's state changes.
            //    The function indicates that when playing a video (state=1),
            //    the player should play for six seconds and then stop.
            var done = false;
            window.onPlayerStateChange = this.onPlayerStateChange;
            // window.onPlayerStateChange = function(event) {
            //     if (event.data == YT.PlayerState.PLAYING && !done) {
            //         setTimeout(stopVideo, 6000);
            //         done = true;
            //     }
            // }

            window.stopVideo = this.stopVideo;
            // window.stopVideo = function() {
            //     player.stopVideo();
            // }
        }
    }
</script>
