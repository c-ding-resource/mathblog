
jQuery(document).ready(function($) {
    var textarea=$('textarea');
    
    var citations='@article{einstein,\n' +
        '    author =       "Albert Einstein",\n' +
        '    title =        "{Zur Elektrodynamik bewegter K{\\"o}rper}. ({German})\n' +
        '        [{On} the electrodynamics of moving bodies]",\n' +
        '    journal =      "Annalen der Physik",\n' +
        '    volume =       "322",\n' +
        '    number =       "10",\n' +
        '    pages =        "891--921",\n' +
        '    year =         "1905",\n' +
        '    DOI =          "http://dx.doi.org/10.1002/andp.19053221004"\n' +
        '}';
    markIt.cite=new Cite(citations);
    textarea.markItUp(mySettings);
    /*var simplemde = new NewMDE();
    var writingSample=$('textarea[name="content"]').val();
    var citations=$('input[name="citations"]').val();
    simplemde.value(writingSample);
    simplemde.citations(citations);

    //DO NOT GET MULTILINE TEXT WITH THE FOLLOWING FUNCTION, IT WILL BE MINIFIED.
    function multilineText(fn){
        return fn.toString().split('\n').slice(1,-1).join('\n') + '\n';
    }*/

});
