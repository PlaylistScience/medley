<template>
    <div class="container">
        <div class="container__left">
            <!-- The part that the youtube iframe api hooks into -->
            <div class="player">
                <div v-if="env === 'PROD' || env === 'DEV'"
                    class="player__embed"
                >
                    <div id="player" class="player__iframe"></div>
                </div>
                <div v-if="env === 'OFFLINE'"
                    class="player__embed player__embed--offline"
                ></div>

                <div class="player__controls">
                    <button class="player__control-button" v-on:click="playTrack(previousTrack())">Previous</button>
                    <button class="player__control-button" v-on:click="playTrack(nextTrack())">Next</button>
                </div>

                <ul v-if="tracks[index]"
                    class="player__trackinfo">
                    <li class="player__trackinfo-item">Artist: {{ tracks[index].artist }}</li>
                    <li class="player__trackinfo-item">Title: {{ tracks[index].name }}</li>
                </ul>
            </div>
        </div>
        <div class="container__right">
            <div class="container__top">
                <div class="people">
                    <h2 class="people__heading">People</h2>
                    <div class="people_list">
                        <div @click="getTracks()"
                            class="people__item"
                        >
                            all
                        </div>
                        <div v-for="user in users" :key="user.id"
                            class="people__item"
                            @click="loadUserTracks(user.id)"
                        >
                            <span>{{ user.email }}</span> - <a :href="'user/' + user.id" >Profile</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container__bottom">
                <div id="tracks" class="tracks">
                    <h2 class="tracks__heading">Tracks</h2>
                    <div class="tracks__list">
                        <div v-for="(track, index) in tracks" :key="track.id"
                            class="tracks__item"
                            :class="isPlayingClass(index)"
                            @click="playTrack(index)"
                        >
                            <a>{{ track.name }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                env: String,
                player: null,
                tracks: <Tracks> [],
                // anyone know why Number needs to be uppercase here but lowercase everywhere else?
                index: Number,
                users: [], // TODO: interface
            }
        },

        methods: {
            playTrack(index: number) {
                // native yt embed api function
                this.player.loadVideoById(this.tracks[index].ytid);
                this.index = index;
            },

            getUsers(callback: (error?: Error) => void) {
                this.$http.get('/api/users').then(response => {
                    this.users = response.body;
                    callback();
                }, response => { // error
                    callback(response);
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

            // creates a waterfall including all necessary functions that need to be run along with getTracks()
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

            // wraps around getUserTracks
            loadUserTracks(id: number) {
                waterfall([
                    (callback: (error: Error) => void) => { this.getUserTracks(id, (error: Error) => { callback(error) }) },
                ], (err, result) => {
                    if (err) throw err;
                    // process result if needed
                });
            },

            // wraps around getUsers
            loadUsers() {
                waterfall([
                    (callback: (error: Error) => void) => { this.getUsers((error: Error) => { callback(error) }) },
                ], (err, result) => {
                    if (err) throw err;
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
                if(event.data === 0) {
                    this.playTrack(this.nextTrack());
                }
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
                return this.index === index ? "tracks__item--playing" : "";
            },
        },

        created() {
            this.env = env; // pass env value to vue
            this.loadTracks();
            this.loadUsers();
        }
    }
</script>
