// Zuck stories and stories data

const timestamp = function () {
    let timeIndex = 0;
    let shifts = [35, 60, 60 * 3, 60 * 60 * 2, 60 * 60 * 25, 60 * 60 * 24 * 4, 60 * 60 * 24 * 10];

    let now = new Date();
    let shift = shifts[timeIndex++] || 0;
    let date = new Date(now - shift * 1000);

    return date.getTime() / 1000;
};

// Stories data
class Zuck {
    constructor(stories1, param2) {
        this.stories = stories1;
        this.options = param2;
        this.id = document.getElementById(this.stories);
        this.currentStory = 0;
        this.currentStoryItem = 0;
        this.playing = false;
        this.seenItems = [];
        this.opened = false;
        this.keyboardEvents = function (e) {
            if (e.keyCode === 13) {
                if (this.opened) {
                    this.close();
                } else {
                    this.open(this.currentStory);
                }
            } else if (e.keyCode === 27) {
                this.close();
            } else if (e.keyCode === 37) {
                this.prev();
            } else if (e.keyCode === 39) {
                this.next();
            }
        };
        this.dragEvents = {
            init: function () {
                this.bindedMove = this.dragEvents.move.bind(this);
                this.bindedEnd = this.dragEvents.end.bind(this);
                this.id.addEventListener("touchmove", this.bindedMove, {passive: true});
                this.id.addEventListener("mousemove", this.bindedMove, {passive: true});
                this.id.addEventListener("touchend", this.bindedEnd, {passive: true});
                this.id.addEventListener("mouseup", this.bindedEnd, {passive: true});
            },
            move: function (e) {
                if (this.opened) {
                    let x = e.pageX || e.touches[0].pageX;
                    let xDiff = this.startX - x;
                    let xPercent = xDiff / window.innerWidth * 100;
                    let xAbs = Math.abs(xDiff);
                    let rotate = 0;
                    let translate = 0;
                    let opacity = 1;
                    let scale = 1;
                    let scaleDiff = 0.2;
                    if (xDiff > 0) {
                        this.dragLeft = true;
                    } else {
                        this.dragLeft = false;
                    }
                    if (this.dragLeft) {
                        translate = xDiff + window.innerWidth;
                        rotate = 1 * xPercent;
                        opacity = 1 - xAbs / window.innerWidth;
                        scale = 1 - (xAbs / window.innerWidth * scaleDiff / 100);
                    } else {
                        translate = xDiff - window.innerWidth;
                        rotate = -1 * xPercent;
                        opacity = 1 - xAbs / window.innerWidth;
                        scale = 1 + (xAbs / window.innerWidth * scaleDiff / 100);
                    }
                }
            },
        }
    }

        // Update your story below
        let stories = new Zuck('stories', {
            backNative: false,    // uses window history to enable back button on browsers/android
            previousTap: true,    // use 1/3 of the screen to navigate to previous item when tap the story
            skin: "snapgram",     // container class
            autoFullScreen: false,// enables fullscreen on mobile browsers
            avatars: true,        // shows user photo instead of last story item preview
            list: false,          // displays a timeline instead of carousel
            openEffect: true,     // enables effect when opening story
            cubeEffect: true,     // enables the 3d cube effect when sliding story
            backButton: true,     // adds a back button to close the story viewer
            /* IMP - turn this reactive: FALSE or leave it commented if not using any framework */
            // reactive: true,    // set true if you use frameworks like React to control the timeline
            rtl: false,           // enable/disable RTL
            localStorage: true,   // set true to save "seen" position. Element must have a id to save properly.
            stories: [
                // First Way (create each story in this way with multiple items)
                Zuck.buildTimelineItem(
                    // story id
                    "user-1",

                    // Story photo src
                    "assets/images/post/1by1/02.jpg",

                    // Story name (or user name)
                    "Judy Nguyen",

                    // Story link (leave empty "" for javascript based zuck)
                    "",
                    // Story time
                    timestamp(),
                    // Story items
                    [
                        // item [id, photo/video, length(sec), src, preview, link, linkText, time, seen(true/false)]
                        ["user1-story1", "photo", 5, "assets/images/albums/01.jpg", "", '', false, false, timestamp()],
                        ["user1-story2", "video", 0, "assets/images/videos/video-call.mp4", "", '', false, false, timestamp()],
                        ["user1-story3", "photo", 5, "assets/images/albums/02.jpg", "", 'https://webestica.com/', 'Visit my Portfolio', false, timestamp()]
                    ]
                ),

                // Story (First way)
                Zuck.buildTimelineItem(
                    "user-2",
                    "assets/images/post/1by1/03.jpg",
                    "Billy Vasquez",
                    "",
                    timestamp(),
                    [
                        ["user2-story1", "photo", 5, "assets/images/albums/03.jpg", "", '', false, false, timestamp()],
                    ]
                ),

                // Story item (First way)
                Zuck.buildTimelineItem(
                    "user-3",
                    "assets/images/post/1by1/04.jpg",
                    "Amanda Reed",
                    "",
                    timestamp(),
                    [
                        ["user3-story1", "photo", 5, "assets/images/albums/04.jpg", "", '', false, false, timestamp()],
                    ]
                ),

                // Story item (First way)
                Zuck.buildTimelineItem(
                    "user-4",
                    "assets/images/post/1by1/05.jpg",
                    "Lori Stevens",
                    "",
                    timestamp(),
                    [
                        ["user4-story1", "photo", 5, "assets/images/albums/05.jpg", "", '', false, false, timestamp()],
                    ]
                ),

                // Story item (First way)
                Zuck.buildTimelineItem(
                    "user-5",
                    "assets/images/post/1by1/06.jpg",
                    "Samuel Bishop",
                    "",
                    timestamp(),
                    [
                        ["user5-story1", "photo", 5, "assets/images/albums/06.jpg", "", '', false, false, timestamp()],
                    ]
                ),

                // Story item (First way)
                Zuck.buildTimelineItem(
                    "user-6",
                    "assets/images/post/1by1/07.jpg",
                    "Joan Wallace",
                    "",
                    timestamp(),
                    [
                        ["user6-story1", "photo", 5, "assets/images/albums/06.jpg", "", '', false, false, timestamp()],
                    ]
                ),

                // Second way (this way implements only single story with multiple sub-stories)
                {
                    id: "user-7",               // story id
                    photo: "assets/images/albums/05.jpg",            // story photo (or user photo)
                    name: "Carolyn Ortiz",             // story name (or user name)
                    link: "",             // story link (useless on story generated by script)
                    lastUpdated: "",      // last updated date in unix time format
                    seen: false,          // set true if user has opened

                    items: [              // array of items
                        // Story item
                        {
                            id: "user7-story1",       // item id
                            type: "photo",      // photo or video
                            length: 3,          // photo timeout or video length in seconds - uses 3 seconds timeout for images if not set
                            src: "assets/images/albums/05.jpg",      // photo or video src
                            preview: "",        // optional - item thumbnail to show in the story carousel instead of the story defined image
                            link: "",           // a link to click on story
                            linkText: "",       // link text
                            time: "",           // optional a date to display with the story item. unix timestamp are converted to "time ago" format
                            seen: false         // set true if current user was read,
                        },
                    ]
                },
            ]
        });
    }
