<template>
    <div>
        <!-- The part that the youtube iframe api hooks into -->
        <div id="player"></div>
        <div class="player--controls">
            <button class="player--controls__button" v-on:click="playTrack(previousTrack())">Previous</button>
            <button class="player--controls__button" v-on:click="playTrack(nextTrack())">Next</button>
        </div>
        <ul>
            <li v-on:click="getTracks()">all</li>
            <li v-for="user in users" :key="user.id">
                <div>
                    <span v-on:click="loadUserTracks(user.id)">{{ user.email }}</span> -
                    <a v-bind:href="'user/' + user.id" >Profile</a>
                </div>
            </li>
        </ul>
        <ul id="tracks" class="tracks">
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

    interface Tracks {
        [position: number]: Track;
    }

    export default {
        data() {
            return {
                player: null,
                tracks: <Tracks> [],
                // anyone know why Number needs to be uppercase here but lowercase everywhere else?
                index: Number,
            }
        },

        methods: {
            playTrack(index: number) {
                // native yt embed api function
                this.player.loadVideoById(this.tracks[index].ytid);
                this.index = index;
            },

            getUsers() {
                this.$http.get('/api/users').then(response => {
                    this.users = response.body;
                }, response => {
                    // error
                });
            },

            getUserTracks(id: number, callback: (error?: Error) => void) {
                this.$http.get(`/api/user/${id}/tracks`).then(response => {
                    this.tracks = response.body;
                    callback();
                }, response => { // error
                    callback(response);
                });
            },

            getTracks(callback: (error?: Error) => void) {
                this.$http.get('/api/tracks').then(response => {
                    this.tracks = response.body;
                    callback();
                }, response => { // error
                    callback(response);
                });
            },

            loadTracks() {
                waterfall([
                    (callback: (error: Error) => void) => { this.getTracks((error: Error) => { callback(error) }) },
                    (callback: (error: Error) => void) => { this.injectLoadYT((error: Error) => { callback(error) }) },
                    (callback: (error: Error) => void) => { this.bind((error: Error) => { callback(error) }) },
                ], (err, result) => {
                    if (err) throw err;
                    // process result if needed
                });
            },

            loadUserTracks(id: number) {
                waterfall([
                    (callback: (error: Error) => void) => { this.getUserTracks(id, (error: Error) => { callback(error) }) },
                ], (err, result) => {
                    if (err) throw err;
                    // process result if needed
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

            // bind youtube's event listeners to our vue functions
            // encore says youtubeiframeapiready does not exist on window... but it do
            // https://i.kym-cdn.com/photos/images/newsfeed/000/476/412/f38.png
            bind(callback: (error: Error) => void) {
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
                this.index = 0;
                this.newYTPlayer(this.tracks[this.index].ytid, (player: any) => {
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
            nextTrack(): Number {
                // TODO: Add checks and bounds
                return this.index += 1;
            },

            previousTrack(): Number {
                // TODO: Add checks and bounds
                return this.index -= 1;
            },

            isPlayingClass(index): String {
                return this.index === index ? "playing" : "";
            },
        },

        created() {
            this.loadTracks();
            this.getUsers();
        }
    }
</script>
