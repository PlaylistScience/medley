<template>
    <div>
        <!-- The part that the youtube iframe api hooks into -->
        <div id="player"></div>
        <ul id="tracks">
            <li v-for="track in tracks" :key="track.id">
                <div>
                    <a v-on:click="playTrack(track)">{{ track.name }}</a>
                </div>
            </li>
        </ul>
    </div>
</template>

<script lang="ts">
    // add some typed rigor
    interface Track {
        id: number,
        name: String,
        url: String,
    }

    interface Tracks {
        [position: number]: Track;
    }

    export default {


        data() {
            return {
                player: null,
                done: <Boolean> false,
                tracks: <Tracks> [],
                track: <Track> {},
            }
        },
        methods: {
            playTrack(track: Track) {

            },
            loadTracks() {
                this.$http.get('/api/tracks').then(response => {
                    // get body data
                    this.tracks = response.body;
                }, response => {
                    // error callback
                    console.error(response);
                });
            },
            injectLoadYT() {
                // create and insert script tag to load youtube's js
                var tag = document.createElement('script');
                tag.src = "https://www.youtube.com/iframe_api";
                var firstScriptTag = document.getElementsByTagName('script')[0];
                firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
            },
            bind() {
                // bind youtube's event listeners to our vue functions
                // encore says youtubeiframeapiready does not exist on window... but it do
                // https://i.kym-cdn.com/photos/images/newsfeed/000/476/412/f38.png
                window.onYouTubeIframeAPIReady = this.onYouTubeIframeAPIReady;
                window.onPlayerReady = this.onPlayerReady;
                window.onPlayerStateChange = this.onPlayerStateChange;
                window.stopVideo = this.stopVideo;
            },
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
                console.log('onPlayerStateChange event');
                console.log(event);
            },
            stopVideo() {
                console.log('stopVideo function');
                this.player.stopVideo();
            }
        },
        created() {
            this.loadTracks();
            this.injectLoadYT();
            this.bind();
        }
    }
</script>
