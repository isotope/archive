var CollectionWizard=new Class({Binds:["send","show","checked"],initialize:function(a){this.element=a;$$(("#ctrl_"+a+" .jserror")).setStyle("display","none");$$(("#ctrl_"+a+" .search")).setStyle("display","table-row");$$(("#ctrl_"+a+" tbody tr")).each(function(c){var b=c.getElement("input[type=checkbox]");if(b){b.addEvent("change",function(d){d.target.getParent("tr").destroy();$(("ctrl_"+a)).send()})}});$(("ctrl_"+a)).set("send",{url:("ajax.php?action=ffl&id="+a),link:"cancel",onSuccess:this.show});$$(("#ctrl_"+this.element+" .search input.tl_text")).addEvent("keyup",this.send)},send:function(){$$(("#ctrl_"+this.element+" .search input.tl_text")).setStyle("background-image","url(system/modules/collectionwizard/html/loading.gif)");$(("ctrl_"+this.element)).send()},show:function(c,d){$$(("#ctrl_"+this.element+" .search input.tl_text")).setStyle("background-image","none");$$(("#ctrl_"+this.element+" tr.found")).each(function(e){e.destroy()});var a=JSON.decode(c);var b=Elements.from(a.content,false);$$(("#ctrl_"+this.element+" tbody")).adopt(b);b.each(function(f){var e=f.getElement("input[type=checkbox]");if(e){e.addEvent("change",this.checked)}}.bind(this))},checked:function(d){if(d.target.checked){var c=$$(("#ctrl_"+this.element+" tbody tr.existing")).length;var e=d.target.getParent("tr");var a=e.getElement("select[class=tl_select]");var f=e.getElement("input[name=products-qty]");var b=e.getElement("input[name=products-price]");e.removeClass("found").inject($$(("#ctrl_"+this.element+" tr.search"))[0],"before").addClass("existing");d.target.set("name","products["+(c)+"][product]");if(a){a.set("name","products["+(c)+"][options]")}f.set("name","products["+(c)+"][qty]");b.set("name","products["+(c)+"][price]")}else{d.target.getParent("tr").destroy();$(("ctrl_"+this.element)).send()}}});