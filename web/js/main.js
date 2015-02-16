$( document ).ready(function() {
    ZeroClipboard.config( { swfPath: "/bower_components/zeroclipboard/dist/ZeroClipboard.swf" } );
    var client = new ZeroClipboard($(".copy-button"));
});