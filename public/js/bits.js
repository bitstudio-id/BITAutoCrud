"use strict";var BITS=function(){let t=()=>{return{CallButton:t=>(t=>{$("#"+t.target).append($("<button />",{id:t.id,class:t.class,type:t.type,text:t.label}))})(t)}};return{rButton:e=>{t().CallButton(e)}}}();