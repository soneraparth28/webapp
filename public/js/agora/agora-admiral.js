// create Agora client
var client = AgoraRTC.createClient({
    mode: "live",
    codec: "vp8"
});
var localTracks = {
    videoTrack: null,
    audioTrack: null
};
var remoteUsers = {};
// Agora client options
var options = {
    uid: userId,
    appid: appIdAgora,
    token: agoraToken,
    channel: agorachannelName,
    role: role,
    // host or audience
    audienceLatency: 2,
    camera: {
        camId: '',
        micId: '',
        stream: {}
    }
};

var cameraVideoProfile = '1080p_5'; // 960 × 720 @ 30fps  & 750kbs
// keep track of devices
var devices = {
    cameras: [],
    mics: []
}
var localTrackState = {
    videoTrackMuted: false,
    audioTrackMuted: false
};

async function joinChannel() {
    // create Agora client

    if (options.role === "audience") {
        client.setClientRole(options.role, {
            level: options.audienceLatency
        });
        // add event listener to play remote tracks when remote user publishs.
        client.on("user-published", handleUserPublished);
        client.on("user-unpublished", handleUserUnpublished);
    }
    else client.setClientRole(options.role);


    // join the channel
    options.uid = await client.join(options.appid, options.channel, options.token || null, options.uid || null);
    if (options.role !== "host") return;


    // create local audio and video tracks
    if (!localTracks.audioTrack) localTracks.audioTrack = await AgoraRTC.createMicrophoneAudioTrack({encoderConfig: "music_standard"});
    if (!localTracks.videoTrack) localTracks.videoTrack = await AgoraRTC.createCameraVideoTrack({encoderConfig: cameraVideoProfile,});
    localTracks.videoTrack.play("full-screen-video"); // play local video track

    // publish local tracks to channel
    await client.publish(Object.values(localTracks));
    console.log("publish success");

    // Get Cameras
    AgoraRTC.getCameras()
        .then(function(cameras) {
            devices.cameras = cameras; // store cameras array
            cameras.forEach(function(camera, i){
                let name = camera.label.split('(')[0];
                let optionId = 'camera_' + i;
                let deviceId = camera.deviceId;
                if (i === 0 && options.camera.camId === '') options.camera.camId = deviceId;
                $('#camera-list').append('<a class="dropdown-item" id="' + optionId + '">' + name + '</a>');
            });
            $('#camera-list a').click(function(event) {
                let index = event.target.id.split('_')[1];
                changeStreamSource(index, "video");
            });
        })
        .catch(function(err) {
            console.log('Error get cameras', err);
        });

    // Get Microphones
    AgoraRTC.getMicrophones()
        .then(function(mics) {
            devices.mics = mics; // store cameras array
            mics.forEach(function(mic, i){
                let name = mic.label.split('(')[0];
                let optionId = 'mic_' + i;
                let deviceId = mic.deviceId;
                if(i === 0 && options.camera.micId === '') options.camera.micId = deviceId;
                if(name.split('Default - ')[1] != undefined) name = '[Default Device]' // rename the default mic - only appears on Chrome & Opera
                $('#mic-list').append('<a class="dropdown-item" id="' + optionId + '">' + name + '</a>');
            });
            $('#mic-list a').click(function(event) {
                let index = event.target.id.split('_')[1];
                changeStreamSource(index, "autio");
            });
        })
        .catch(function(err) {
            console.log('Error get Microphones', err);
        });
}


function changeStreamSource(deviceIndex, deviceType) {
    let deviceId;
    if (deviceType === "video") {
        deviceId = devices.cameras[deviceIndex].deviceId;

        localTracks.videoTrack.setDevice(deviceId);
        options.camera.camId = deviceId;
        localTracks.videoTrack.setEncoderConfiguration(cameraVideoProfile);
    }

    if (deviceType === "audio") {
        deviceId = devices.mics[deviceIndex].deviceId;

        localTracks.audioTrack.setDevice(deviceId);
        options.camera.camId = deviceId;
    }
}

async function leave() {
    for (trackName in localTracks) {
        var track = localTracks[trackName];
        if (track) {
            track.stop();
            track.close();
            localTracks[trackName] = undefined;
        }
    }

    // remove remote users and player views
    remoteUsers = {};
    $("#full-screen-video").html("");

    // leave the channel
    await client.leave();
    console.log("client leaves channel success");
}
async function subscribe(user, mediaType) {
    await client.subscribe(user, mediaType); // subscribe to a remote user
    console.log("subscribe success");
    if (mediaType === 'video') user.videoTrack.play('full-screen-video');
    if (mediaType === 'audio') user.audioTrack.play();

    if(options.role === "audience") {
        let container = $("#full-screen-video").first();
        container.attr("style", "");
        if(container.find("#video-muted-text").length) container.find("#video-muted-text").first().remove();
    }


    setTimeout(function () {
        let clientUser = (client._users).filter(function (clientUser) {
            return clientUser.uid === user.uid;
        })
        if(!empty(clientUser)) {
            clientUser = clientUser[ (Object.keys(clientUser)[0]) ];
            if(clientUser._video_muted_ || !clientUser._video_added_) setNoVideoFeed();
        }
    }, 300);
}
function handleUserPublished(user, mediaType) {
    //print in the console log for debugging
    console.log('"user-published" event for remote users is triggered.');
    let id = user.uid;
    remoteUsers[id] = user;
    subscribe(user, mediaType);
}
function handleUserUnpublished(user, mediaType) {
    //print in the console log for debugging
    console.log('Video unpublished');
    if (mediaType === 'video') {
        if(options.role === "audience") setNoVideoFeed();
        let id = user.uid;
        delete remoteUsers[id];
    }
}
function setNoVideoFeed() {
    $("#full-screen-video").first().attr("style", "background-color: black;")
    .html(
        '<div id="video-muted-text" class="bg-transparent flex-col-around flex-align-center w-100 px-4 h-100">' +
        '<p class="font-30 color-light mxw-70 text-center">Video feed currently hidden</p>' +
        '</div>'
    );
}

function toggleMuteVideo(btn) { return localTrackState.videoTrackMuted ? unmuteVideo(btn) : muteVideo(btn); }
function toggleMuteAudio(btn) { return localTrackState.audioTrackMuted ? unmuteAudio(btn) : muteAudio(btn); }

async function muteVideo(btn) {
    if (!localTracks.videoTrack) return;
    await localTracks.videoTrack.setMuted(true);
    localTrackState.videoTrackMuted = true;
    btn.find("i").first().removeClass("bi-camera-video-fill").addClass("bi-camera-video-off");
    btn.find("span").first().text("Unmute video")
}
async function unmuteVideo(btn) {
    if (!localTracks.videoTrack) return;
    await localTracks.videoTrack.setMuted(false);
    localTrackState.videoTrackMuted = false;
    btn.find("i").first().removeClass("bi-camera-video-off").addClass("bi-camera-video-fill");
    btn.find("span").first().text("Mute video")
}
async function muteAudio(btn) {
    if (!localTracks.audioTrack) return;
    await localTracks.audioTrack.setMuted(true);
    localTrackState.audioTrackMuted = true;
    btn.find("i").first().removeClass("fa-volume-up").addClass("fa-volume-mute");
    btn.find("span").first().text("Unmute audio")
}
async function unmuteAudio(btn) {
    if (!localTracks.audioTrack) return;
    await localTracks.audioTrack.setMuted(false);
    localTrackState.audioTrackMuted = false;
    btn.find("i").first().removeClass("fa-volume-mute").addClass("fa-volume-up");
    btn.find("span").first().text("Mute audio")
}


