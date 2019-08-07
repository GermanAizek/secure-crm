// If absolute URL from the remote server is provided, configure the CORS
// header on that server.
//var url = 'http://www.pdf995.com/samples/pdf.pdf';

var allfiles;

let urlObject = new URL(window.location.href);
let nameFile = new URLSearchParams(urlObject.search);
var url = "docs/" + nameFile.get('file');
console.log(url);

function setAllFiles(files) {
    allfiles = files;
    console.log(allfiles);
}

// The workerSrc property shall be specified.
pdfjsLib.workerSrc = '//mozilla.github.io/pdf.js/build/pdf.worker.js';

var pdfDoc = null,
  pageNum = 1,
  pageRendering = false,
  pageNumPending = null,
  scale = 1,
  canvaslst = [],
  context = [];

canvaslst.push(document.getElementById('right'));
context.push(canvaslst[0].getContext('2d'));
canvaslst.push(document.getElementById('left'));
context.push(canvaslst[1].getContext('2d'));

/**
 * Get page info from document, resize canvas accordingly, and render page.
 * @param num Page number.
 */
function renderPage(num) {
  var index = num % 2;
  var canvas = canvaslst[index];
  var ctx = context[index];
  pageRendering = true;
  // Using promise to fetch the page
  pdfDoc.getPage(num).then(function(page) {
    var viewport = page.getViewport(scale);
    canvas.height = viewport.height;
    canvas.width = viewport.width;

    // Render PDF page into canvas context
    var renderContext = {
      canvasContext: ctx,
      viewport: viewport
    };
    var renderTask = page.render(renderContext);

    // Wait for rendering to finish
    renderTask.promise.then(function() {
      pageRendering = false;
      if (pageNumPending !== null) {
        // New page rendering is pending
        renderPage(pageNumPending);
        pageNumPending = null;
      }
    });
  });

  // Update page counters
  document.getElementById('page_num').textContent = pageNum;
  document.getElementById('pleft').textContent = pageNum;
  document.getElementById('pright').textContent = pageNum + 1;
}

/**
 * If another page rendering in progress, waits until the rendering is
 * finised. Otherwise, executes rendering immediately.
 */
function queueRenderPage(num) {
  if (pageRendering) {
    pageNumPending = num;
  } else {
    renderPage(num);
    renderPage(num + 1)
  }
}

/**
 * Displays previous page.
 */
function onPrevPage() {
  if (pageNum <= 1) {
    return;
  }
  pageNum -= 2;
  queueRenderPage(pageNum);
}
document.getElementById('prev').addEventListener('click', onPrevPage);
document.getElementById('left').addEventListener('click', onPrevPage);

/**
 * Displays next page.
 */
function onNextPage() {
  if (pageNum + 1 >= pdfDoc.numPages) {
    return;
  }
  pageNum += 2;
  queueRenderPage(pageNum);
}
document.getElementById('next').addEventListener('click', onNextPage);
document.getElementById('right').addEventListener('click', onNextPage);


/**
 * Asynchronously downloads PDF.
 */
pdfjsLib.getDocument(url).then(function(pdfDoc_) {
  pdfDoc = pdfDoc_;
  document.getElementById('page_count').textContent = pdfDoc.numPages;

  // Initial/first page rendering
  renderPage(pageNum);
  renderPage(pageNum + 1);
});

function gettext() {
  var pdf = pdfjsLib.getDocument(url);
  return pdf.then(function(pdf) { // get all pages text
       var maxPages = pdf.numPages;
       var countPromises = []; // collecting all page promises
       for (var j = 1; j <= maxPages; j++) {
          var page = pdf.getPage(j);

          var txt = "";
          countPromises.push(page.then(function(page) { // add page promise
              var textContent = page.getTextContent();
              return textContent.then(function(text){ // return content promise
                  return text.items.map(function (s) { return s.str; }).join(''); // value page text 

              });
          }));
       }
       // Wait for all pages and join text
       return Promise.all(countPromises).then(function (texts) {
         
         return texts.join('');
       });
  });
}

function findContentAllFiles() {
    // waiting on gettext to finish completion, or error
    gettext().then(function (text) {
      let searchText = document.getElementById("searchText").value;
      if (text.includes(searchText)) {
          let newUrl = "?file=" + url.split('/')[1];

          let result = document.createElement('a');
          result.setAttribute('href', newUrl);
          result.setAttribute('id', newUrl);
          result.className = "list-group-item list-group-item-action flex-column";
          result.innerHTML = '<p class="mb-1">' + searchText + "</p>";

          let resultname = document.createElement('div');
          resultname.setAttribute('href', newUrl);
          resultname.className = "d-flex w-100 justify-content-between";
          resultname.innerHTML = '<h5 class="mb-1">' + url.split('/')[1] + '</h5>';

          document.getElementById('list-search-results').append(result);
          document.getElementById(newUrl).append(resultname);
      }
    }, function (reason) {
      console.error(reason);
    });
}
document.getElementById('search-all').addEventListener('click', findContentAllFiles);

// Loaded via <script> tag, create shortcut to access PDF.js exports.
// var pdfjsLib = window['pdfjs-dist/build/pdf'];

// // The workerSrc property shall be specified.
// pdfjsLib.GlobalWorkerOptions.workerSrc = '//mozilla.github.io/pdf.js/build/pdf.worker.js';

// var pdfDoc = null,
//     pageNum = 1,
//     pageRendering = false,
//     pageNumPending = null,
//     scale = 1,
//     canvas = document.getElementById('viewer-window'),
//     ctx = canvas.getContext('2d');

// /**
//  * Get page info from document, resize canvas accordingly, and render page.
//  * @param num Page number.
//  */
// function renderPage(num) {
//   pageRendering = true;
//   // Using promise to fetch the page
//   pdfDoc.getPage(num).then(function(page) {
//     console.log(page.getTextContent());
//     var viewport = page.getViewport({scale: scale});
//     canvas.height = viewport.height;
//     canvas.width = viewport.width;

//     // Render PDF page into canvas context
//     var renderContext = {
//       canvasContext: ctx,
//       viewport: viewport
//     };
//     var renderTask = page.render(renderContext);

//     // Wait for rendering to finish
//     renderTask.promise.then(function() {
//       pageRendering = false;
//       if (pageNumPending !== null) {
//         // New page rendering is pending
//         renderPage(pageNumPending);
//         pageNumPending = null;
//       }
//     });
//   });

//   // Update page counters
//   document.getElementById('page_num').textContent = num;
// }

// /**
//  * If another page rendering in progress, waits until the rendering is
//  * finised. Otherwise, executes rendering immediately.
//  */
// function queueRenderPage(num) {
//   if (pageRendering) {
//     pageNumPending = num;
//   } else {
//     renderPage(num);
//   }
// }

// /**
//  * Displays previous page.
//  */
// function onPrevPage() {
//   if (pageNum <= 1) {
//     return;
//   }
//   pageNum--;
//   queueRenderPage(pageNum);
// }
// document.getElementById('prev').addEventListener('click', onPrevPage);

// /**
//  * Displays next page.
//  */
// function onNextPage() {
//   if (pageNum >= pdfDoc.numPages) {
//     return;
//   }
//   pageNum++;
//   queueRenderPage(pageNum);
// }
// document.getElementById('next').addEventListener('click', onNextPage);

// /**
//  * Asynchronously downloads PDF.
//  */
// pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
//   pdfDoc = pdfDoc_;
//   document.getElementById('page_count').textContent = pdfDoc.numPages;

//   // Initial/first page rendering
//   renderPage(pageNum);
// });

// clear url get params
//window.history.replaceState({}, document.title, "/index.html");