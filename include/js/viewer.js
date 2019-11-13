
let urlObject = new URL(window.location.href);
let nameFile = new URLSearchParams(urlObject.search);
var url = "../../docs/" + nameFile.get('file');
// alert(url);
// TODO: доделай viewer.js

function isMobile(a) {
  return (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4)));
}

// ONE SIDE VIEW
// var pdfjsLib = window['pdfjs-dist/build/pdf'];

// The workerSrc property shall be specified.
// pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@2.1.266/build/pdf.worker.min.js';  

var w = window.innerWidth;
var h = window.innerHeight;

var heightPage;
var widthPage;
var scale;

var sizeDoc = document.getElementById('sizeDoc').value;

if (isMobile(navigator.userAgent || navigator.vendor || window.opera)) {
  heightPage = h*0.8;
  widthPage = w*0.8;
  scale = (heightPage/widthPage)*0.4;
} else {
  heightPage = h;
  widthPage = w;
  scale = (widthPage/heightPage)*sizeDoc;
}

'use strict';

if (!pdfjsLib.getDocument || !pdfjsViewer.PDFSinglePageViewer) {
  alert('Please build the pdfjs-dist library using\n' +
        '  `gulp dist-install`');
}

// The workerSrc property shall be specified.
//
pdfjsLib.GlobalWorkerOptions.workerSrc =
  '../pdfjs/node_modules/pdfjs-dist/build/pdf.worker.js';

// Some PDFs need external cmaps.
//
var CMAP_URL = '../pdfjs/node_modules/pdfjs-dist/cmaps/';
var CMAP_PACKED = true;

var container = document.getElementById('viewerContainer');

var pageNum = 1;
var pagePrevNum = 1;
var returnPages = [];

var pdfDocument = null;

// (Optionally) enable hyperlinks within PDF files.
var pdfLinkService = new pdfjsViewer.PDFLinkService();

// (Optionally) enable find controller.
var pdfFindController = new pdfjsViewer.PDFFindController({
  linkService: pdfLinkService,
});

var pdfSinglePageViewer = new pdfjsViewer.PDFSinglePageViewer({
  container: container,
  linkService: pdfLinkService,
  findController: pdfFindController,
});
pdfLinkService.setViewer(pdfSinglePageViewer);

document.addEventListener('pagesinit', function () {
  // We can use pdfSinglePageViewer now, e.g. let's change default scale.
  pdfSinglePageViewer.currentScaleValue = 'page-fit';
  pdfSinglePageViewer.currentScale = sizeDoc;
  var resumepage = sessionStorage.getItem('viewerpage');
  pdfSinglePageViewer.currentPageNumber = resumepage;
  document.getElementById('page_num').textContent = resumepage;
  // alert(page);

  // document.getElementById("returnLink").hidden = false;

  // if (SEARCH_FOR) { // We can try search for things
  //   pdfFindController.executeCommand('find', { query: SEARCH_FOR, });
  // }
});

document.addEventListener("pagechanging", function() {
  pagePrevNum = pageNum;
  pageNum = pdfSinglePageViewer.currentPageNumber;
  document.getElementById('page_num').textContent = pageNum;
  document.getElementById('returnLink').textContent = "Назад на";

  if (pagePrevNum+1 < pageNum || pagePrevNum > pageNum) {
    if (returnPages.indexOf(pagePrevNum) == -1) {
        returnPages.push(pagePrevNum);
        for(var i = 0; i < returnPages.length; i++) {
            var opt = returnPages[i];
            document.getElementById('returnPagesList').innerHTML += "<option>" + opt + "</option>";
        }
    }
  }

  sessionStorage.setItem('viewerpage', pageNum);
});

// Loading document.
function render(urldoc) {
  var loadingTask = pdfjsLib.getDocument({
    url: urldoc,
    cMapUrl: CMAP_URL,
    cMapPacked: CMAP_PACKED,
  });
  loadingTask.promise.then(function(_pdfDocument) {
    pdfDocument = _pdfDocument;
    document.getElementById('page_count').textContent = pdfDocument.numPages;
    document.getElementById('page_num').textContent = pageNum;
    // Document loaded, specifying document for the viewer and
    // the (optional) linkService.
    pdfSinglePageViewer.setDocument(pdfDocument);

    pdfLinkService.setDocument(pdfDocument, null);
  });
}

$("#sizeDoc").on("input change", function() {
  sizeDoc = document.getElementById('sizeDoc').value;
  document.getElementById('zoom').textContent = sizeDoc;
  render(url);
});

render(url);

function onPrevPage() {
  if (pageNum <= 1) {
    return;
  }

  pageNum--;
  pdfSinglePageViewer.currentPageNumber = pageNum;
  document.getElementById('page_num').textContent = pageNum;
}
document.getElementById('prev').addEventListener('click', onPrevPage);

/**
 * Displays next page.
 */
function onNextPage() {
  if (pageNum >= pdfDocument.numPages) {
    return;
  }

  pageNum++;
  pdfSinglePageViewer.currentPageNumber = pageNum;
  document.getElementById('page_num').textContent = pageNum;
}
document.getElementById('next').addEventListener('click', onNextPage);

function onReturnPage() {
  var list = document.getElementById("returnPagesList");
  if (list.options[list.selectedIndex]) {
    pageNum = parseInt(list.options[list.selectedIndex].text);
    pdfSinglePageViewer.currentPageNumber = parseInt(list.options[list.selectedIndex].text);
    document.getElementById('page_num').textContent = parseInt(list.options[list.selectedIndex].text);
  }
}
document.getElementById('returnLink').addEventListener('click', onReturnPage);

// SEARCH FUNCTIONS

// function gettext() {
//   var pdf = pdfjsLib.getDocument(url);
//   return pdf.then(function(pdf) { // get all pages text
//        var maxPages = pdf.numPages;
//        var countPromises = []; // collecting all page promises
//        for (var j = 1; j <= maxPages; j++) {
//           var page = pdf.getPage(j);

//           var txt = "";
//           countPromises.push(page.then(function(page) { // add page promise
//               var textContent = page.getTextContent();
//               return textContent.then(function(text){ // return content promise
//                   return text.items.map(function (s) { return s.str; }).join(''); // value page text 

//               });
//           }));
//        }
//        // Wait for all pages and join text
//        return Promise.all(countPromises).then(function (texts) {
         
//          return texts.join('');
//        });
//   });
// }

function findInFile() {
    var searchCaseSens = $("#searchCaseSens").is(":checked");
    var phraseSearch = $("#phraseSearch").is(":checked");
    var searchText = document.getElementById("searchText").value;

    pdfFindController.executeCommand('find', {
      caseSensitive: searchCaseSens, 
      findPrevious: undefined,
      highlightAll: true,
      phraseSearch: phraseSearch,
      query: searchText
    });

    document.getElementById("findBar").hidden = false;
}
document.getElementById('search-button').addEventListener('click', findInFile);

function findInAllFiles() {
    // get all access files
    $.ajax({
        type: "POST",
        url: "viewer.php",
        dataType: 'json',
        data: {
          getAccessCatalogs: "true"
        },
        success : function(text) {
            text.forEach(function(entry) {
                console.log(entry);
                // TODO: тут все файлы надо пройтись и проверить совпадения
            });
        },
        error: function(data) {
          alert(data.responseText);
        }
    });

    // foreach all files
    // find and push to list
    var searchCaseSens = $("#searchCaseSens").is(":checked");
    var phraseSearch = $("#phraseSearch").is(":checked");
    var searchText = document.getElementById("searchText").value;
    pdfFindController.executeCommand('find', {
      caseSensitive: searchCaseSens, 
      findPrevious: undefined,
      highlightAll: true, 
      phraseSearch: phraseSearch,
      query: searchText
    });

    // waiting on gettext to finish completion, or error
    // gettext().then(function (text) {
    //   let searchText = document.getElementById("searchText").value;
    //   if (text.includes(searchText)) {
    //       let newUrl = "?file=" + url.split('/')[1];

    //       let result = document.createElement('a');
    //       result.setAttribute('href', newUrl);
    //       result.setAttribute('id', newUrl);
    //       result.className = "list-group-item list-group-item-action flex-column";
    //       result.innerHTML = '<p class="mb-1">' + searchText + "</p>";

    //       let resultname = document.createElement('div');
    //       resultname.setAttribute('href', newUrl);
    //       resultname.className = "d-flex w-100 justify-content-between";
    //       resultname.innerHTML = '<h5 class="mb-1">' + url.split('/')[1] + '</h5>';

    //       document.getElementById('list-search-results').append(result);
    //       document.getElementById(newUrl).append(resultname);
    //   }
    // }, function (reason) {
    //   console.error(reason);
    // });
}
document.getElementById('searchInAll').addEventListener('click', findInAllFiles);

function findPrevMatch() {
  pdfFindController._updateAllPages();
  pdfFindController._reset();

  sessionStorage.setItem('viewerpage', pageNum);
  // pdfFindController._nextPageMatch();
}
document.getElementById('findPrev').addEventListener('click', findNextMatch);

function findNextMatch() {
  pdfFindController._nextMatch();

  sessionStorage.setItem('viewerpage', pageNum);
  // pdfFindController._nextPageMatch();
}
document.getElementById('findNext').addEventListener('click', findNextMatch);
