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
            const coursedetail={
                wbs:e.target.getAttribute('data-wbs'),
                startdate:e.target.getAttribute('data-startdate'),
                enddate:e.target.getAttribute('data-enddate')
            }
            
            startRequestServer(PDF,customerid,coursedetail,trainee_list);
            window.console.log(e.target);
        })
    });
}

const startRequestServer=(PDF,customerid,coursedetail,trainee_list)=>{
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
        formData.append(`params[${i}][wbs]`,coursedetail.wbs);
        formData.append(`params[${i}][startdate]`,coursedetail.startdate);
        formData.append(`params[${i}][enddate]`,coursedetail.enddate);
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
    const font = await pdfDoc.embedFont(PDF.StandardFonts.Helvetica);
    const fontBold = await pdfDoc.embedFont(PDF.StandardFonts.HelveticaBold);
    listTrainees=response;
    let page= pdfDoc.addPage();
    const { width, height } = page.getSize();

    // Create a string of text and measure its width and height in our custom font
    const text = 'Course report';
    const textSize = 24;
    const textWidth = font.widthOfTextAtSize(text, textSize);
    const textHeight = font.heightAtSize(textSize);
    const verticalGap=textHeight/2;

    // Draw the string of text on the page
    page.drawText(text, {
      x: width/2-textWidth/2,
      y: height-textHeight-verticalGap,
      size: textSize,
      font: font,
      color: PDF.rgb(0, 0.53, 0.71),
    })

    const courseTitle=listTrainees[0].coursename;
    const textCourseSize=16;
    const textCourseTitleWidth = font.widthOfTextAtSize(courseTitle, textCourseSize);
    const textCourseTitleHeight = font.heightAtSize(textCourseSize);

    // Draw the string of text on the page
    page.drawText(courseTitle, {
        x: width/2-textCourseTitleWidth/2,
        y: height-textCourseTitleHeight-verticalGap-textHeight-verticalGap,
        size: textCourseSize,
        font: font,
        color: PDF.rgb(0, 0.53, 0.71),
      })
    //Convert dates in miliseconds
    let startdate=new Date(listTrainees[0].startdate*1000);
    let day=startdate.getDate();
    let month=startdate.getMonth()+1;
    let year=startdate.getFullYear();
    let formattedStartDate=`${day.toString().padStart(2, '0')}-${month.toString().padStart(2, '0')}-${year}`;
    
    let enddate=new Date(listTrainees[0].enddate*1000);
    day=enddate.getDate();
    month=enddate.getMonth()+1;
    year=enddate.getFullYear();
    let formattedEndDate=`${day.toString().padStart(2, '0')}-${month.toString().padStart(2, '0')}-${year}`;
    
    const textBody=`Below is a detailed list of the grades obtained by the trainees for the course ${listTrainees[0].coursename} that took place on the dates: ${formattedStartDate} to ${formattedEndDate}.`
    const textBodySize=14;
    const textBodyWidth = font.widthOfTextAtSize(textBody, textBodySize);
    const textBodyHeight = font.heightAtSize(textBodySize);
    page.drawText(textBody, {
        x: 40,
        y: height-textCourseTitleHeight-verticalGap-textHeight-verticalGap-textBodyHeight-verticalGap-verticalGap,
        size: textBodySize,
        font: font,
        color: PDF.rgb(0, 0, 0),
        maxWidth: width-80, wordBreaks: [" "]
      })

    let lineHeightPos=height-180;
    page.drawLine({
    start: { x: 40, y: lineHeightPos },
    end: { x: width-40, y: lineHeightPos },
    thickness: 1,
    color: PDF.rgb(0, 0.53, 0.71),
    opacity: 0.5,
    });
    const field1='ID Number';
    const textFieldSize=12;
    const textField1Width = font.widthOfTextAtSize(field1, textFieldSize);
    const textField1Height = font.heightAtSize(textFieldSize);
    const verticalFieldGap=textField1Height/2;
    page.drawText( field1, {
        x: 55,
        y: height - 180 + textField1Height,
        size: textFieldSize,
        font: fontBold,
        color: PDF.rgb(0, 0, 0)
    });
    const field2="Name"
    const textField2Width = font.widthOfTextAtSize(field2, textFieldSize);
    page.drawText( field2, {
        x: 55+textField1Width+textField2Width,
        y: height - 180 + textField1Height,
        size: textFieldSize,
        font: fontBold,
        color: PDF.rgb(0, 0, 0)
    });

    const field3="Attendance"
    const textField3Width = font.widthOfTextAtSize(field3, textFieldSize);
    const field4="Assessment"
    const textField4Width = font.widthOfTextAtSize(field4, textFieldSize);
    page.drawText( field3, {
        x: width-55-textField3Width-textField4Width-30,
        y: height - 180 + textField1Height,
        size: textFieldSize,
        font: fontBold,
        color: PDF.rgb(0, 0, 0)
    });

    page.drawText( field4, {
        x: width-55-textField4Width,
        y: height - 180 + textField1Height,
        size: textFieldSize,
        font: fontBold,
        color: PDF.rgb(0, 0, 0)
    });


    listTrainees.map((trainee,index)=>{
        if (index==10 || index==20 || index==30 || index==40 || index==50){
            page=pdfDoc.addPage();

            // Draw the string of text on the page
            page.drawText(text, {
                x: width/2-textWidth/2,
                y: height-textHeight-verticalGap,
                size: textSize,
                font: font,
                color: PDF.rgb(0, 0.53, 0.71),
            })

            page.drawText(courseTitle, {
                x: width/2-textCourseTitleWidth/2,
                y: height-textCourseTitleHeight-verticalGap-textHeight-verticalGap,
                size: textCourseSize,
                font: font,
                color: PDF.rgb(0, 0.53, 0.71),
            })
            
            page.drawText(textBody, {
                x: 40,
                y: height-textCourseTitleHeight-verticalGap-textHeight-verticalGap-textBodyHeight-verticalGap-verticalGap,
                size: textBodySize,
                font: font,
                color: PDF.rgb(0, 0, 0),
                maxWidth: width-80, wordBreaks: [" "]
            })
            lineHeightPos=height-180;
            page.drawLine({
                start: { x: 40, y: lineHeightPos },
                end: { x: width-40, y: lineHeightPos },
                thickness: 1,
                color: PDF.rgb(0, 0.53, 0.71),
                opacity: 0.5,
            });

            page.drawText( field1, {
                x: 55,
                y: height - 180 + textField1Height,
                size: textFieldSize,
                font: fontBold,
                color: PDF.rgb(0, 0, 0)
            });

            page.drawText( field2, {
                x: 55+textField1Width+textField2Width,
                y: height - 180 + textField1Height,
                size: textFieldSize,
                font: fontBold,
                color: PDF.rgb(0, 0, 0)
            });

            page.drawText( field3, {
                x: width-55-textField3Width-textField4Width-30,
                y: height - 180 + textField1Height,
                size: textFieldSize,
                font: fontBold,
                color: PDF.rgb(0, 0, 0)
            });

            page.drawText( field4, {
                x: width-55-textField4Width,
                y: height - 180 + textField1Height,
                size: textFieldSize,
                font: fontBold,
                color: PDF.rgb(0, 0, 0)
            });

        } //End if
        let textIdNumberWith=font.widthOfTextAtSize(trainee.billid,textFieldSize);
        let textFieldHeight=font.heightAtSize(textFieldSize);
        
            lineHeightPos-=25;  
        
        
        page.drawText( trainee.billid, {
            x: 55,
            y: lineHeightPos+textFieldHeight/2,
            size: textFieldSize,
            font: font,
            color: PDF.rgb(0, 0, 0)
        });
        let textNameWith=font.widthOfTextAtSize(trainee.firstname +', ' +trainee.lastname,textFieldSize);
        page.drawText( trainee.firstname +', ' +trainee.lastname, {
            x: 55+textField1Width+textField2Width,
            y: lineHeightPos+textFieldHeight/2,
            size: textFieldSize,
            font: font,
            color: PDF.rgb(0, 0, 0)
        });
        let attendance=(trainee.att===null)?'-':(trainee.att*1).toFixed(2);
        
        page.drawText( attendance, {
            x: width-100-textField3Width-textField4Width+textField3Width/2,
            y: lineHeightPos+textFieldHeight/2,
            size: textFieldSize,
            font: font,
            color: PDF.rgb(0, 0, 0)
        });

        let assessment=(trainee.ass===null)?'-':(trainee.ass*1).toFixed(2);
        
        page.drawText( assessment, {
            x: width-75-textField4Width+textField4Width/2,
            y: lineHeightPos+textFieldHeight/2,
            size: textFieldSize,
            font: font,
            color: PDF.rgb(0, 0, 0)
        });

        page.drawLine({
            start: { x: 40, y: lineHeightPos },
            end: { x: width-40, y: lineHeightPos },
            thickness: 1,
            color: PDF.rgb(0, 0.53, 0.71),
            opacity: 0.5,
        });

    })

    
    

    const listComputedAtt=listTrainees.filter(elem=>{
        return elem.att!==null
    })

    const listComputedAss=listTrainees.filter(elem=>{
        return elem.ass!==null
    });

    let avgAtt=listComputedAtt.reduce((acumulador, elementoActual) => acumulador + Math.floor(elementoActual.att), 0)/listComputedAtt.length;
    avgAtt=avgAtt.toFixed(2);
    let avgAss=listComputedAss.reduce((acumulador, elementoActual) => acumulador + Math.floor(elementoActual.ass), 0)/listComputedAss.length;
    avgAss=avgAss.toFixed(2);

    //Total score for the course
    page.drawText( 'Total course average: ', {
        x: width-350,
        y: lineHeightPos-24,
        size: textFieldSize,
        font: fontBold,
        color: PDF.rgb(0, 0, 0)
    });

    
    page.drawText( avgAtt+' %', {
        x: width-100-textField3Width/2-textField4Width,
        y: lineHeightPos-24,
        size: textFieldSize,
        font: fontBold,
        color: PDF.rgb(0, 0, 0)
    });
    
    
    page.drawText( ''+avgAss, {
        x: width-75-(textField4Width/2),
        y: lineHeightPos-24,
        size: textFieldSize,
        font: fontBold,
        color: PDF.rgb(0, 0, 0)
    });



    const pdfDataUri=await pdfDoc.saveAsBase64({dataUri:true});
    window.open(pdfDataUri);
};

