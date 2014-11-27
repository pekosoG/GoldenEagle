var base64EncodeChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
var base64DecodeChars = new Array(
    -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
    -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
    -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 62, -1, -1, -1, 63,
    52, 53, 54, 55, 56, 57, 58, 59, 60, 61, -1, -1, -1, -1, -1, -1,
    -1,  0,  1,  2,  3,  4,  5,  6,  7,  8,  9, 10, 11, 12, 13, 14,
    15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -1, -1, -1, -1, -1,
    -1, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
    41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -1, -1, -1, -1, -1);

function base64encode(str) {
    var out, i, len;
    var c1, c2, c3;

    len = str.length;
    i = 0;
    out = "";
    while(i < len) {
	c1 = str.charCodeAt(i++) & 0xff;
	if(i == len)
	{
	    out += base64EncodeChars.charAt(c1 >> 2);
	    out += base64EncodeChars.charAt((c1 & 0x3) << 4);
	    out += "==";
	    break;
	}
	c2 = str.charCodeAt(i++);
	if(i == len)
	{
	    out += base64EncodeChars.charAt(c1 >> 2);
	    out += base64EncodeChars.charAt(((c1 & 0x3)<< 4) | ((c2 & 0xF0) >> 4));
	    out += base64EncodeChars.charAt((c2 & 0xF) << 2);
	    out += "=";
	    break;
	}
	c3 = str.charCodeAt(i++);
	out += base64EncodeChars.charAt(c1 >> 2);
	out += base64EncodeChars.charAt(((c1 & 0x3)<< 4) | ((c2 & 0xF0) >> 4));
	out += base64EncodeChars.charAt(((c2 & 0xF) << 2) | ((c3 & 0xC0) >>6));
	out += base64EncodeChars.charAt(c3 & 0x3F);
    }
    return out;
}

function base64decode(str) {
    var c1, c2, c3, c4;
    var i, len, out;

    len = str.length;
    i = 0;
    out = "";
    while(i < len) {
	/* c1 */
	do {
	    c1 = base64DecodeChars[str.charCodeAt(i++) & 0xff];
	} while(i < len && c1 == -1);
	if(c1 == -1)
	    break;

	/* c2 */
	do {
	    c2 = base64DecodeChars[str.charCodeAt(i++) & 0xff];
	} while(i < len && c2 == -1);
	if(c2 == -1)
	    break;

	out += String.fromCharCode((c1 << 2) | ((c2 & 0x30) >> 4));

	/* c3 */
	do {
	    c3 = str.charCodeAt(i++) & 0xff;
	    if(c3 == 61)
		return out;
	    c3 = base64DecodeChars[c3];
	} while(i < len && c3 == -1);
	if(c3 == -1)
	    break;

	out += String.fromCharCode(((c2 & 0XF) << 4) | ((c3 & 0x3C) >> 2));

	/* c4 */
	do {
	    c4 = str.charCodeAt(i++) & 0xff;
	    if(c4 == 61)
		return out;
	    c4 = base64DecodeChars[c4];
	} while(i < len && c4 == -1);
	if(c4 == -1)
	    break;
	out += String.fromCharCode(((c3 & 0x03) << 6) | c4);
    }
    return out;
}
/*adjust data type*/
/*add for support ajax by lijiechun*/
function CreateXMLHttp()
{	
     var xmlhttp = null;
     var aVersions = ["MSXML2.XMLHttp.5.0","MSXML2.XMLHttp.4.0","MSXML2.XMLHttp.3.0",      
                      "MSXML2.XMLHttp","Microsoft.XMLHttp"];

     if(window.XMLHttpRequest)
     { 
         try 
         {
             xmlhttp = new XMLHttpRequest();
         }
         catch (e)
         {
         }
     }
     else 
     {
         if(window.ActiveXObject)//IE6��IE5
         {     
             for (var i=0; i<5; i++)   
             {
                  try
                  {          
                       xmlhttp = new ActiveXObject(aVersions[i]);
                       return xmlhttp;
                  }
                  catch (e)
                  {
                  }
             }
          }
     } 
     return xmlhttp;
}
/*add for support ajax by lijiechun*/
function isValidAscii(val)
{
    for ( var i = 0 ; i < val.length ; i++ )
    {
        var ch = val.charAt(i);
        if ( ch < ' ' || ch > '~' )
        {
            return ch;
        }
    }
    return '';
}

function isValidCfgStr(cfgName, val, len)
{
	if (isValidAscii(val) != '')         
	{            
		//alert(cfgName + top.g_oErrFrame.MACRO_INVALID_CHAR + isValidAscii(val) + '".')          
		return false;       
    }
   if (val.length > len)
   {
	   //alert(cfgName + top.g_oErrFrame.MACRO_NOT_EXCEED + len  + top.g_oErrFrame.MACRO_CHAR);
	   return false;
   }	
   return true;
}

function isHexaDigit(digit) {
   var hexVals = new Array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
                           "A", "B", "C", "D", "E", "F", "a", "b", "c", "d", "e", "f");
   var len = hexVals.length;
   var i = 0;
   var ret = false;

   for ( i = 0; i < len; i++ )
      if ( digit == hexVals[i] ) break;

   if ( i < len )
      ret = true;

   return ret;
}

function isSafeStringExc(compareStr, UnsafeStr)
{
	for (var i = 0; i < compareStr.length; i++)
	{
		var c = compareStr.charAt(i);
		if (isValidAscii(c) != '')
		{
			 return false;
	    }
		else
		{
			if (UnsafeStr.indexOf(c) > -1)
			{
				return false;
			}
		}
	}
    return true;
}

function isSafeStringIn(compareStr, UnsafeStr)
{
	for (var i = 0; i < compareStr.length; i++)
	{
		var c = compareStr.charAt(i);
		if (isValidAscii(c) != '')
		{
			 return false;
	    }
		else
		{
			if (UnsafeStr.indexOf(c) == -1)
			{
				return false;
			}
		}
	}
    return true;
}

// Check if a name valid
function isValidName(name) 
{
   //return isSafeStringExc(name,'!"~<>;{}|%*\\^[]`+$,=\'#&: \t');
   return isSafeStringExc(name,'\\');
}

//a valid string do not contain '"' and each char is validAscII
function isValidString(name) 
{
	if (isValidAscii(name) == '')
    {
		return true;
	}
	else
	{
		return false;
	}
}

function isInteger(value)
{   	
	if (/^(\+|-)?\d+$/.test(value)) 
	{
	   return true;
	} 
	else 
	{
	    return false;
	}
}

function isPlusInteger(value)
{
	if (isInteger(value) && parseInt(value) >= 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function isFloat(value)
{   	
	if (/^(\+|-)?\d+($|\.\d+$)/.test(value))
	{
	   return true;
	} 
	else 
	{
	   return false;
	}
}

function isValidCfgInteger(cfgName, val, start, end)
{
	   if (isInteger(val) == false)
	   {
	       alert(cfgName + top.g_oErrFrame.MACRO_INVALID_AND_NUM);
		   return false;
	   }
	   var temp = parseInt(val);
	   if (temp < start || temp > end)
	   {
	       alert(cfgName + top.g_oErrFrame.MACRO_GREATER + start.toString()
		         + top.g_oErrFrame.MACRO_LESS + end.toString() + '.');
		   return false;
	   }	
}

//web Element 
/*get element by name or id*/
function getElById(sId)
{
	return getElement(sId);
}

function getElementById(sId)
{
	if (document.getElementById)
	{
		return document.getElementById(sId);	
	}
	else if (document.all)
	{
		// old IE
		return document.all(sId);
	}
	else if (document.layers)
	{
		// Netscape 4
		return document.layers[sId];
	}
	else
	{
		return null;
	}
}

/*getElByName*/
function getElementByName(sId)
{    // standard
	if (document.getElementsByName)
	{
		var element = document.getElementsByName(sId);
		
		if (element.length == 0)
		{
			return null;
		}
		else if (element.length == 1)
		{
			return 	element[0];
		}
		
		return element;		
	}
}

function getElement(sId)
{
	 var ele = getElementByName(sId); 
	 if (ele == null)
	 {
		 return getElementById(sId);
	 }
	 return ele;
}

function getOptionIndex(sId,sValue)
{
	var selObj = getElement(sId);
	if (selObj == null)
	{
		return -1;
	}
	
	for (i = 0; i < selObj.length; i++)
	{
		if (selObj.options[i].value == sValue)
		{
			return i;
		}
	}
	
	return -1;
}

/*----------------getLength-----------------*/
function getValue(sId)
{
	var item;
	if (null == (item = getElement(sId)))
	{
		debug(sId + " is not existed" );
		return -1;
	}

	return item.value;
}


/* Web page manipulation functions */
function setDisplay (sId, sh)
{
    var status;
    if (sh > 0) 
	{
        status = "";
    }
    else 
	{
        status = "none";
    }

	getElement(sId).style.display = status;
}

function setVisible(sId, sh)
{
    var status;
    if (sh > 0) 
	{
        status = "visible";
    }
    else 
	{
        status = "hidden";
    }
    
    getElement(sId).style.visibility = status;
}

function setSelect(sId, sValue)
{
	var item;
	if (null == (item = getElement(sId)))
	{
		debug(sId + " is not existed" );
		return false;
	}
	
	for (var i = 0; i < item.options.length; i++) 
	{
        if (item.options[i].value == sValue)
		{

        	item.selectedIndex = i;
        	return true;
        }
    }

    debug("the option which value is " + sValue + " is not existed in " + sId);
    return false;
}

function setText (sId, sValue)
{
	var item;
	if (null == (item = getElement(sId)))
	{
		debug(sId + " is not existed" );
		return false;
	}
    
	item.value = sValue;
	return true;
}


function setCheck(sId, value)
{
	var item;
	if (null == (item = getElement(sId)))
	{
		debug(sId + " is not existed" );
		return false;
	}
	
    if ( value == '1' ) 
	{    
       item.checked = true;
    }
	else
	{
       item.checked = false;
    }

    return true;
}

function setRadio(sId, sValue)
{
	var item;
	if (null == (item = getElement(sId)))
	{
		debug(sId + " is not existed" );
		return false;
	}
	
	for (i=0; i<item.length; i++)
	{
		if (item[i].value == sValue) 
		{
			item[i].checked = true;
			return true;
		}
    }

    debug("the option which value is " + sValue + " is not existed in " + sId);
    return false;
}

function setDisable(sId, flag)
{
	var item;
	if (null == (item = getElement(sId)))
	{
		debug(sId + " is not existed" );
		return false;
	}
	
    if ( flag == 1 || flag == '1' ) 
	{
         item.disabled = true;
    }
	else
	{
         item.disabled = false;
    }     

    return true;
}

function getCheckVal(sId)
{
	var item;
	if (null == (item = getElement(sId)))
	{
		debug(sId + " is not existed" );
		return -1;
	}
	if (item.checked == true)
	{
		return 1;
	}

	else
	{
		return 0;
	}
}

function getRadioVal(sId)
{
	var item;
	if (null == (item = getElement(sId)))
	{
		debug(sId + " is not existed" );
		return -1;
	}
//	debug(item.children[0])
	
	for (i = 0; i < item.length; i++)
	{
		if (item[i].checked == true)
		{
		   return item[i].value;
		}
	}
	
	return -1;
}

function getSelectVal(sId)
{
   return getValue(sId);
}

/////////////////////////////////////////////////////
// Load / submit functions
function webSubmitForm(sFormName, DomainNamePrefix)
{
    /*-----------------------internal method------------------------*/
    this.setPrefix = function(Prefix){
		if (Prefix == null)
		{
			this.DomainNamePrefix = '.';
		}
		else
		{
			this.DomainNamePrefix = Prefix + '.';
		}
	};
	
	this.getDomainName = function(sName){
		if (this.DomainNamePrefix == '.')
		{
		    return sName;
		}
		else
		{
		    return this.DomainNamePrefix + sName;
		}
	};
	
    this.getNewSubmitForm = function()
	{
		var submitForm = document.createElement("FORM");
		document.body.appendChild(submitForm);
		submitForm.method = "POST";
		return submitForm;
	};
	
	this.createNewFormElement = function (elementName, elementValue){
	    var newElement = document.createElement('INPUT');
		newElement.setAttribute('name',elementName);
		newElement.setAttribute('value',elementValue);
		newElement.setAttribute('type','hidden');
		return newElement;
	};
	
	/*---------------------------external method----------------------------*/
	this.addForm = function(sFormName,DomainNamePrefix){
	    this.setPrefix(DomainNamePrefix);
	    var srcForm = getElement(sFormName);
		if (srcForm != null && srcForm.length > 0 && this.oForm != null 
			&& srcForm.style.display != 'none')
		{
			MakeCheckBoxValue(srcForm);
			
			for(i=0; i < srcForm.elements.length; i++)
			{  
			     var type = srcForm.elements[i].type;
			     if (type != 'button' && srcForm.elements[i].disabled == false)
				 {				
					 if (this.DomainNamePrefix != '.')
					 {
						 var ele = this.createNewFormElement(this.DomainNamePrefix 
												              + srcForm.elements[i].name,
												              srcForm.elements[i].value);	
						 this.oForm.appendChild(ele);
					 }	   
					 else
					 {
						var ele = this.createNewFormElement(srcForm.elements[i].name,
												             srcForm.elements[i].value
															  );
						this.oForm.appendChild(ele);
					 }	 
				 }
			 }
		}
		else
		{
			this.status = false;
		}
		
		this.DomainNamePrefix = '.';
	};
    
	this.addDiv = function(sDivName,Prefix)
	{
		// this.setPrefix(DomainNamePrefix);
		if (Prefix == null)
		{
			Prefix = '';
		}
		else
		{
			Prefix += '.';
		}
		
		var srcDiv = getElement(sDivName);	
		if (srcDiv == null)
		{
			debug(sDivName + ' is not existed!');
			return;
		}
		if (srcDiv.style.display == 'none')
		{
			return;
		}
		//debug(srcDiv)
		var eleSelect = srcDiv.getElementsByTagName("select");
		if (eleSelect != null)
	    {
			for (k = 0; k < eleSelect.length; k++)
			{
				if (eleSelect[k].disabled == false)
				{
					this.addParameter(Prefix+eleSelect[k].name,eleSelect[k].value);
				}
			}
		}
		
		MakeCheckBoxValue(srcDiv);
		var eleInput = srcDiv.getElementsByTagName("input");
		if (eleInput != null)
	    {
			for (k = 0; k < eleInput.length; k++)
			{
				if (eleInput[k].type != 'button' && eleInput[k].disabled == false)
				{
				    this.addParameter(Prefix+eleInput[k].name,eleInput[k].value);
				}
			}	
		}
	};
	
	this.addParameter = function(sName, sValue){
		
		var DomainName = this.getDomainName(sName);
		
		for(i=0; i < this.oForm.elements.length; i++) 
		{
			if(this.oForm.elements[i].name == DomainName)
			{
				this.oForm.elements[i].value = sValue;
				this.oForm.elements[i].disabled = false;
				return;
			}
		}
	
		
		if(i == this.oForm.elements.length) 
		{	
			var ele = this.createNewFormElement(DomainName,sValue);	
			this.oForm.appendChild(ele);
		}
	};
	
    this.disableElement = function(sName){	
	    var DomainName = this.getDomainName(sName);		
		for(i=0; i < this.oForm.elements.length; i++) 
		{
			if(this.oForm.elements[i].name == DomainName)
			{
				this.oForm.elements[i].disabled = true;
				return;
			}
		}
	};
	
    this.usingPrefix = function(Prefix){
	     this.DomainNamePrefix = Prefix + '.';
	};
	
    this.endPrefix = function(){
	     this.DomainNamePrefix = '.';
	};
	
	this.setMethod = function(sMethod) {
		if(sMethod.toUpperCase() == "GET")
			this.oForm.method = "GET";
		else
			this.oForm.method = "POST";
	};

	this.setAction = function(sUrl) {
		this.oForm.action = sUrl;
	};

	this.setTarget = function(sTarget) {
		this.oForm.target = sTarget;
	};

	this.submit = function(sURL, sMethod) {
	        top.g_page_submiting = 1;
		if( sURL != null && sURL != "" ) this.setAction(sURL);
		if( sMethod != null && sMethod!= "" ) this.setMethod(sMethod);
		
		if (this.status == true)
		    this.oForm.submit();
	};
	
	this.status = true;


	/*--------------------------------excute by internal-------------------------*/
	this.setPrefix(DomainNamePrefix);
	this.oForm = this.getNewSubmitForm();
	if (sFormName != null && sFormName != '')
	{
		this.addForm(sFormName,this.DomainNamePrefix);
		//this.DomainNamePrefix = '.';
	}
}

function MakeCheckBoxValue(srcForm)
{
	var Inputs = srcForm.getElementsByTagName("input");
	for (var i = 0; i < Inputs.length; i++) 
	{
		if (Inputs[i].type == "checkbox")
		{
			var CheckBox = getElement(Inputs[i].name);
//debug(Inputs[i].name)
			if (CheckBox.checked == true)
			{
				CheckBox.value = 1;
			}
			else
			{
				CheckBox.value = 0;
			}
		}
		else if (Inputs[i].type == "radio")
		{
			var radio = getElement(Inputs[i].name);
	        for (k = 0; k < radio.length; k++)
			{
				if (radio[k].checked == false)
				{
				    radio[k].disabled = true;
				}				
			}
		}
	}
}


function Submit(type)
{
    if (CheckForm(type) == true)
	{
	    var Form = new webSubmitForm();
	    AddSubmitParam(Form,type);		
	    top.g_page_submiting = 1;
	    Form.submit();
	}
}

function debug(info)
{
	//alert(info);
}

function convert16to10(from)
{
    var len = from.length;
    var mode = 1;
    var to = 0;
    var onechar;
    var oneint;
    if (from == null)
    {
        return null;
    }
    for(var i=len-1; i>=0; i--)
    {
        onechar = from.charAt(i);
        switch(onechar)
        {
             case "a":
             case "A":
             	 oneint = 10;
             	 break;
             case "b":
             case "B":
             	 oneint = 11;
             	 break;
             case "c":
             case "C":
             	 oneint = 12;
             	 break;
             case "d":
             case "D":
             	 oneint = 13;
             	 break;
             case "e":
             case "E":
             	 oneint = 14;
             	 break;
             case "f":
             case "F":
             	 oneint = 15;
             	 break;
             default:
             	 oneint = parseInt(onechar);
             	 break;
        }          	 
        to = to + mode*oneint;
        mode = mode*16;
    }
    return to;
}
