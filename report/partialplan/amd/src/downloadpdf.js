export const init=(PDF,customerid)=>{
    let link=document.querySelectorAll('.courseFinished');
    link.forEach(element => {
        element.addEventListener('click',(e)=>{
            e.preventDefault();
            let trainee_list=e.target.getAttribute('data-trainees').split(',');
            trainee_list=trainee_list.reduce((arr,elem)=>{
                const aux= elem.split("_");
                return [...arr,{group:aux[0],billid:aux[1]}];
            },[]);
            const wbs=e.target.getAttribute('data-wbs');
            startRequestServer(PDF,customerid,wbs,trainee_list);
            window.console.log(e.target);
        })
    });
}

const startRequestServer=(PDF,customerid,wbs,trainee_list)=>{
    const xhr=new XMLHttpRequest();
    const url=window.location.protocol+'//'+window.location.hostname+'/webservice/rest/server.php';
    xhr.open('POST',url,true);
    const formData= new FormData();
    const token=document.querySelector('#id_token').value;
    formData.append('wstoken',token);
    formData.append('wsfunction','report_partialplan_get_assessment');
    formData.append('moodlewsrestformat','json');
    for (let i=0;i<trainee_list.length;i++){
        const group=trainee_list[i].group.trim();
        const billid=trainee_list[i].billid.trim();
        formData.append(`params[${i}][customerid]`,customerid);
        formData.append(`params[${i}][group]`,group);
        formData.append(`params[${i}][billid]`,billid);
        formData.append(`params[${i}][wbs]`,wbs);
    }            
    xhr.send(formData);
    xhr.onload = (event) =>{
        onLoadFunction(PDF,xhr);
        };
    xhr.onprogress = (event)=>{
        onProgressFunction(event);
    } 
    xhr.onerror = function() {
        window.console.log("Solicitud fallida");
    };
}

const onLoadFunction=(PDF,myXhr)=>{
    if (myXhr.readyState=== 4 && myXhr. status === 200){
      const res=JSON.parse(myXhr.response);
      window.console.log(res);
      //Prepare the excel for to be downloaded
      createPdf(PDF,res);
  }
  }

const onProgressFunction= (event)=>{
    if (event.lengthComputable) {
      window.console.log(`Recibidos ${event.loaded} de ${event.total} bytes`);
    } else {
      window.console.log(`Recibidos ${event.loaded} bytes`); // sin Content-Length
    }
}

async function createPdf(PDF,response){
    const pdfDoc = await PDF.PDFDocument.create();
        const helvetica = await pdfDoc.embedFont(PDF.StandardFonts.Helvetica);
          const helveticaBold = await pdfDoc.embedFont(PDF.StandardFonts.HelveticaBold);
        const pdfDataUri=await pdfDoc.saveAsBase64({dataUri:true});
        window.open(pdfDataUri);
};
