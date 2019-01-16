<template>
    <div>
        <!-- The part that the youtube iframe api hooks into -->
        <div id="player"></div>
        <button class="player--button" v-on:click="nextTrack()">Next</button>
        <ul id="tracks">
            <!-- TODO 2 loops to render the current one at the top -->
            <li v-for="(track, index) in tracks" :key="track.id">
                <div>
                    <a v-bind:class="isPlayingClass(index)" v-on:click="playTrack(index)">{{ track.name }}</a>
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
        ytid: String,
    }

    export default {
        data() {
            return {
                player: null,
                tracks: [],
                currentIndex: 0
            }
        },
        methods: {
            playTrack(int: index) {
                this.currentIndex = index;
                this.track = this.tracks[this.currentIndex]; // Could be computed property?
                this.player.loadVideoById(track.ytid); // native yt embed api function
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
                // manually set first track
                // then feed the id manually into the new YT player
                // this is a workaround to accomodate Youtube's expected way of handling this API
                this.track = this.tracks[0];
                this.newYTPlayer(this.tracks[0].ytid, (player: any) => {
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
            // player
            nextTrack() {
                //bounds check yo self before you reck yourself
                this.currentIndex++;
            },
            isPlayingClass(int: index): String {
                return index === this.currentIndex ? "playing" : "";
            },
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
