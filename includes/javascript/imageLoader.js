imageLoader = {
    needtoload: 0,
    loaded: 0,
    state: 0,
    imgs: {},
    'loadimages': function (links, callback) {
        imageLoader.loaded = 0;
        imageLoader.needtoload = links.length;
        for (link in links) {
            imageLoader.loadimage(links[link], callback);
        }

        if (links.length == 0){
            callback()
        }
    },
    checkendload: function (callback,c) {
        if (imageLoader.needtoload == imageLoader.loaded) {
            if (callback !== undefined)  {
                callback()
            }
        }

    },
    loadimage: function (link, callback) {
        if(typeof link !== 'string'){

        }else if (link in imageLoader.imgs ) {
            imageLoader.loaded++;
            imageLoader.checkendload(callback)
        } else {

            img = new Image();
            img.onload = function () {
                imageLoader.loaded += 1;
                imageLoader.checkendload(callback, 'img')

            }
            img.src = link;
            imageLoader.imgs[link] = img;
        }
    }
}