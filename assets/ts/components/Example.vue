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
    import waterfall from "async-waterfall";

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
                tracks: <Tracks> [],
                track: <Track> {},
                index: <Number> 0,
            }
        },

        methods: {
            playTrack(track: Track) {
                this.player.loadVideoById(this.getYTId(track.url)); // native yt embed api function
            },

            loadTracks(callback: (error?: Error) => void) {
                this.$http.get('/api/tracks').then(response => {
                    this.tracks = response.body;
                    callback();
                }, response => { // error
                    callback(response);
                });
            },

            injectLoadYT(callback: (error: Error) => void) {
                // create and insert script tag to load youtube's js
                var tag = document.createElement('script');
                tag.src = "https://www.youtube.com/iframe_api";
                var firstScriptTag = document.getElementsByTagName('script')[0];
                firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
                callback(null);
            },

            bind(callback: (error: Error) => void) {
                // bind youtube's event listeners to our vue functions
                // encore says youtubeiframeapiready does not exist on window... but it do
                // https://i.kym-cdn.com/photos/images/newsfeed/000/476/412/f38.png
                window.onYouTubeIframeAPIReady = this.onYouTubeIframeAPIReady;
                window.onPlayerReady = this.onPlayerReady;
                window.onPlayerStateChange = this.onPlayerStateChange;
                window.stopVideo = this.stopVideo;
                callback(null);
            },

            onYouTubeIframeAPIReady() {
                this.newYTPlayer(this.getYTId(this.tracks[this.index].url), (player: any) => {
                    this.player = player;
                });
            },

            newYTPlayer(videoId: string, callback: (player: any) => void) {
                callback(new YT.Player('player', {
                    videoId,
                    events: {
                        onReady: this.onPlayerReady,
                        onStateChange: this.onPlayerStateChange,
                        onError: this.onPlayerError,
                    }
                }));
            },

            onPlayerReady(event) {
                event.target.playVideo();
            },

            onPlayerStateChange(event) {
                console.log('onPlayerStateChange event', event);
            },

            stopVideo() {
                this.player.stopVideo();
            },

            onPlayerError(event) {
                console.log('enError function', event);
            },

            getYTId(url: string): string {
                // TODO: outsource this logic to a class
                return url.replace(/.*watch\?v=/, '');
            }
        },

        created() {
            waterfall([
                (callback: (error: Error) => void) => { this.loadTracks((error: Error) => { callback(error) }) },
                (callback: (error: Error) => void) => { this.injectLoadYT((error: Error) => { callback(error) }) },
                (callback: (error: Error) => void) => { this.bind((error: Error) => { callback(error) }) },
            ], (err, result) => {
                if (err) throw err;
                // process result
            });
        }
    }
</script>
