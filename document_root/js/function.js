/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var seznamSources = new Array();
var googleSources = new Array();
var seznamCount = -1;

var dataSeznam;
var words;
var webId;




function google(word){
    this.word = word;
    this.count = -1;
}

function seznam(word){
    this.word = word;
    this.count = -1;
}

/**
 * DEPRECATED
 */
function doRequest(url){
    $.getJSON("http://query.yahooapis.com/v1/public/yql?"+
        "q=select%20*%20from%20html%20where%20url%3D%22"+
        encodeURIComponent(url)+
        "%22&format=xml'&callback=?",
        function(data){
            //if(data.results[0]){
                console.log(url);
                console.log('google: ' + data.results[0]);
                seznamCount++;
//            } else {
//                var errormsg = '<p>Error: could not load the page.</p>';
//                console.log('chyba');
//            }
        }).success(console.log(arguments)).error(console.log("error")).complete(arguments);
}


function initVar(arraySeznam, splContent, web_id){
    dataSeznam = arraySeznam;
    words = splContent;
    webId = web_id;
    console.log(dataSeznam);
}

function doAjaxRequest(url, obj, engine){
    var ajax = $.ajax({
        url: "http://query.yahooapis.com/v1/public/yql?"+
        "q=select%20*%20from%20html%20where%20url%3D%22"+
        encodeURIComponent(url)+
        "%22&format='xml'&callback=?",
        //async: false,
        dataType: 'json',
        type: 'GET',
        success: function (data) {
            obj.count++;

            var instr = url.match(/from=\d+/);
            var page = instr[0].match(/\d+/);

            var array = new Array();
            array['data'] = data.results[0];
            array['page'] = url.match(/from=\d{1,2}/i)[0].match(/\d{1,2}/)[0];

            if(seznamSources[obj.word] === undefined){
                seznamSources[obj.word] = new Array();
                //seznamSources[obj.word][page] = data.results[0];
                //seznamSources[obj.word][results][page[0]] = data.results[0];
            }else{
                //seznamSources[obj.word][page] = data.results[0];
                //seznamSources[obj.word][results][page[0]] = data.results[0];
            }
            var pageInt = parseInt(page);
            var pageIndex = null;
            switch(pageInt){
                case 1:
                    pageIndex = 0
                    break;
                case 21:
                    pageIndex = 1
                    break
                case 41:
                    pageIndex = 2
                    break
                case 61:
                    pageIndex = 3
                    break
                case 81:
                    pageIndex = 4
                    break
            }

            seznamSources[obj.word][pageIndex] = data.results[0];

// if(seznamSources[obj.word]['pages'] === undefined){
// seznamSources[obj.word]['pages'] = new Array();
// }
//
// seznamSources[obj.word]['pages'][obj.count] = page[0];
        },

        error: function(){
            console.log('error');
            obj.count++;
            var instr = url.match(/from=\d+/);
            var page = instr[0].match(/\d+/)
            seznamSources[obj.word][results][page[0]] = null;
        },

        timeout: function(){
            console.log("neco");
        },

        complete: function(){
            var bool = true;
            for(var key in dataSeznam){
                if(dataSeznam[key].count < 4){
                    bool = false;
                }
            }
//            for(var i in seznamSources){
//                if(seznamSources[i].length < 4){
//                    bool = false;
//                    break;
//                }
//            }

            if(bool){
                console.log("))))))))))))))))))))))))))))))))))");
                isComplete();
            }
        }
    });
}

function isComplete(){
    console.log(seznamSources);
    var JSONstring = new Object();
    JSONstring.webId = webId;
    JSONstring.results = new Array();
    JSONstring.words = new Array();

    var count = 0;
    //console.log(seznamSources);
    for(var key in seznamSources){
        JSONstring.results[count] = new Object();
        console.log(key);
        JSONstring.results[count].word = key;
        JSONstring.results[count].data = new Array();
        for(var row in seznamSources[key]){
            JSONstring.results[count].data[row] = seznamSources[key][row];

            //JSONstring.results[count].data['page'] = seznamSources[key]['pages'][key];
            
        }
//        for(var page in seznamSources[key]['pages']){
//        }
        count++;
    }

    for(var i in JSONstring.results){
        console.log("----"  + i + "-----");
        console.log(JSONstring.results[i].data.length);
        for(var pruchod in JSONstring.results[i].data){
            console.log(pruchod);
        }
    }

    for(var i in words){
        JSONstring.words[i] = words[i];
    }
//    $.post("?do=sig", JSONstring);
//    
//    console.log('------');
//    console.log(JSONstring);
//    console.log('------');
    console.log(dataSeznam.length);
//    $.post("?do=sig", JSONstring);
}





