<template>
    <div>
        <!-- The part that the youtube iframe api hooks into -->
        <div id="player"></div>
    </div>
</template>

<script lang="ts">
    export default {
        data() {
            return {
                player: null,
                done: false,
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
                        'onReady': this.onPlayerReady,
                        'onStateChange': this.onPlayerStateChange
                    }
                });
            },
            onPlayerReady(event) {
                console.log('onPlayerReady function');
                event.target.playVideo();
            },
            onPlayerStateChange(event) {
                console.log('onPlayerStateChange function');
                if (event.data == YT.PlayerState.PLAYING && !this.done) {
                    setTimeout(this.stopVideo, 6000);
                    this.done = true;
                }
            },
            stopVideo() {
                console.log('stopVideo function');
                this.player.stopVideo();
            }
        },
        created() {

            // create and insert script tag to load youtube's js
            var tag = document.createElement('script');

            tag.src = "https://www.youtube.com/iframe_api";
            var firstScriptTag = document.getElementsByTagName('script')[0];
            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

            // bind youtube's event listeners to our vue functions
            window.onYouTubeIframeAPIReady = this.onYouTubeIframeAPIReady;
            window.onPlayerReady = this.onPlayerReady;
            window.onPlayerStateChange = this.onPlayerStateChange;
            window.stopVideo = this.stopVideo;
        }
    }
</script>
