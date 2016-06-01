function Arrow(sp, ep, st){
		this.sp = sp;
		this.ep = ep;
		this.st = st;
		this.size = 0;
		this.sharp = 0;
	}

	Arrow.prototype.paint = function(context) {
		var sp = this.sp;
		var ep = this.ep;

		context.beginPath();
		context.moveTo(sp.x, sp.y);
		context.lineTo(ep.x, ep.y);

		var h = this._calcH(sp, ep, context);
		context.moveTo(ep.x, ep.y);
		context.lineTo(h.h1.x, h.h1.y);
		context.moveTo(ep.x, ep.y);
		context.lineTo(h.h2.x, h.h2.y);
		context.stroke();
	};

	Arrow.prototype._calcH = function(sp, ep, context){
		var theta = Math.atan( (ep.x - sp.x) / (ep.y - sp.y));
		var cep = this._scrollXOY(ep, -theta);
		var csp = this._scrollXOY(sp, -theta);
		var ch1 = {x:0, y:0};
		var ch2 = {x:0, y:0};

		var l = cep.y - csp.y;
		ch1.x = cep.x + l * (this.sharp || 0.025);
		ch1.y = cep.y - l * (this.size || 0.025);
		ch2.x = cep.x - l * (this.sharp || 0.025);
		ch2.y = cep.y - l * (this.size || 0.025);
		var h1 = this._scrollXOY(ch1, theta);
		var h2 = this._scrollXOY(ch2, theta);

		return {
			h1: h1,
			h2: h2
		};
	};

	Arrow.prototype._scrollXOY = function(p, theta){
		return {
			x: p.x * Math.cos(theta)+p.y*Math.sin(theta),
			y: p.y * Math.cos(theta)-p.x*Math.sin(theta)
		};		
	};

	Arrow.prototype.setPara = function(args){
		this.size = args.arrow_size;
		this.sharp = args.arrow_sharp;
	};

    var canvasWidth = 800, canvasHeight = 600, 
    textWidthMax = 0,// the whole width of the word text will be drawn.
    canvas, context, Max = 10, CurX = 10, CurY = 10,
    fontGap = 3,
    fontSize = 10,
    siblingGap = 20;
    level = 0, 
    gap = 12, 
    nodeWidth = 0, 		//node width
    interlHeight = 0,	//layer height
    times = 6;
	nodeTree = null, 
	textwidth = 0, 	//word text width
	position = 0,	//
	pattern="", //which will be highlight
	pszLeafInfo = [""], leavesData = "", 
	prePosition = 0, postPosition = 0, cursor = 0;
	
   
    function init(){
    	initTreeData();
    	initCanvas();
    }

    function initTreeData(){
		var txt = document.getElementById("textInput").value;
        var txtNew = txt.substr(1,txt.length-2);
        var pNode = new NodeStr();
        ParseInput(pNode, txtNew);
        nodeTree = new Tree();
        nodeTree.begin = pNode;
        GetTreeSent(nodeTree.begin, pszLeafInfo, true);
        leavesData = getLeavesData(pszLeafInfo);
		SetLeafInfo( nodeTree.begin );
    }

	function getCanvasBoundary(){
		canvasWidth = window.innerWidth || document.body.clientWidth;
		canvasHeight = window.innerHeight || document.body.clientHeight;
	}

	function initCanvas(){
		getCanvasBoundary();
			
		canvas = document.getElementById("tree");
        context = canvas.getContext("2d");
        if( context == null ){
            window.alert("error");
        }
		
		canvas.width = canvasWidth ;
 		canvas.height = canvasHeight;
	}

	function onRangeChange(evt){
		// console.log(evt.target.value);
		fontSize = parseInt(evt.target.value);
		reDrawTree();
	}
	
	function reDrawTree(){
		cursor = 0;

		setCanvasBoundry();			//set the boundary of the canvas
		initCanvasContext(); 		//set the context font and curX 
        DrawParsingTree(nodeTree,5);

		reInitcancas();
		initCanvasContext();
		reLocation( nodeTree.begin );
		updateTreeLocation2(nodeTree.begin);
		reDraw( nodeTree.begin );
	}
	
    function onClick(){
		textWidthMax = 0;
		level = 0;

        if( nodeTree != null && nodeTree.begin != null){
			drawOriginTree();
        }else{
        	return;
        }
		
		reInitcancas();
		initCanvasContext();
		
		reLocation( nodeTree.begin );
		updateTreeLocation2(nodeTree.begin);
		reDraw(nodeTree.begin);
    }
		
	function setPosition(){
		prePosition = leavesData.indexOf(pattern);
		postPosition = prePosition + pattern.length;
	}

	function getLeavesData(pszLeafInfo){
		var str = pszLeafInfo[0].split(" ");
		var len = str.length;
		var string ="";
		for (var i = 0; i < len; i++) {
			str[i] =  str[i].trim();
			var preIndex = str[i].indexOf("<");
			var postIndex = str[i].indexOf(">");
			if( preIndex >= 0 ){
				string += str[i].substring(preIndex+1, postIndex);
			}else{
				string += str[i];
			}
		};
		return string;
	}

	function getOneLeafLength( word ){
		var width = 0;
		if( word.indexOf("<") >= 0){
			var preIndex = word.indexOf("<");
			var postIndex = word.indexOf(">");
			width = postIndex - preIndex-1;
		}else{
			var str = word.split("/D");
			width = str[0].trim().length;
		}
		return width;
	}
	
	function drawOriginTree(){
		initCanvasContext();
		DrawParsingTree(nodeTree,5);	
	}
	
	function initCanvasContext(){		//set the curX and font info of the context
		CurX = 5;
		var fontFamily = "Georgia";		
		context.font = fontSize+"px "+ fontFamily ;	
	}
	
	function reInitcancas(){		//reget the boundary of the canvas
		canvasWidth = ( textWidthMax > canvasWidth ) ? (textWidthMax + 80):canvasWidth;
		canvasHeight = ( level*interlHeight> canvasHeight )?(level*interlHeight + 60):canvasHeight;
		setCanvasBoundry();
	}
	
	function setCanvasBoundry(){	//reset the canvas width and height
		canvas.width = canvasWidth;
		canvas.height = canvasHeight;
		context.clearRect(0,0,canvasWidth,canvasHeight);
	}
	
	function reDraw( pNode ){
		var nodeQueue = [];
		nodeQueue.push( pNode );
		while( nodeQueue.length ){
			var tmpNode = nodeQueue.shift();
			reDrawOneNode(tmpNode);

			var chNode = tmpNode.childNode;
			while( chNode ){
				reDrawLine(tmpNode, chNode);
				nodeQueue.push( chNode );
				chNode = chNode.nextNode;
			}	
		}
	}
	
	function reDrawLine(pNodeA, pNodeB){
        context.beginPath();
        context.strokeStyle = "blue";
        var sp = {x:(pNodeA.right+pNodeA.left)/2,
        		  y:pNodeA.bottom+6};
       	var ep = {x:(pNodeB.right+pNodeB.left)/2,
       			  y:pNodeB.top
       	}
	    var arrow = new Arrow(sp,ep,{});
	    arrow.paint(context);
	}
	
	function reDrawOneNode( pNode ){
		var pattern_test = new RegExp(/(\S+)\/D\:(\S+)/);
		if ( pattern_test.test(pNode.word) ) {
			var arr = pattern_test.exec(pNode.word);
			var sp = {
				x: (pNode.parentNode.left + pNode.parentNode.right)/2,
				y:pNode.parentNode.bottom
			};

			var ep = {
				x: (pNode.left + pNode.right)/2,
				y: pNode.top
			};
			context.textAlign="start";
			context.fillText(arr[2], (sp.x + ep.x)/2, (sp.y + ep.y)/2,  context.measureText(arr[2]).width);
			context.textAlign="center";
			context.fillText(arr[1], (pNode.left + pNode.right)/2, pNode.bottom + fontGap, textwidth);
			console.log( RegExp.$1 );
		}else{
			context.textAlign="center";
			context.fillText(pNode.word, (pNode.left + pNode.right)/2, pNode.bottom + fontGap, textwidth);
		}
	}
	
	function reLocation( pNode ){//reset the left and right position. the center of the children's width
		if( !pNode.childNode ){
			return ;
		}
		
		var pChildNode = pNode.childNode;
		while( pChildNode ){
			reLocation( pChildNode );
			pChildNode = pChildNode.nextNode;
		}
		
		var pChild = pNode.childNode;
		var left = pChild.left;
		while( pChild.nextNode ){
			pChild = pChild.nextNode;
		}
		var right = pChild.right;

		var width = pNode.right - pNode.left;
		pNode.left = (pNode.left > (right + left - width)/2 ) ? pNode.left : (right + left - width)/2 ;
		pNode.right = pNode.left + width ;
	}
	
	function updateTreeLocation2(pNode){
		var nodeQueue = [];
		nodeQueue.push( pNode );
		var preNode = null;
		while( nodeQueue.length ){
			var tempQueue = [];
			preNode = null;
			while( nodeQueue.length ){
				var tmpNode = nodeQueue.shift();
				tempQueue.push(tmpNode);
				updateNodeLocation(preNode, tmpNode);
				preNode = tmpNode.childNode;
				while( preNode.nextNode ){
					preNode = preNode.nextNode;
				}
			}

			while( tempQueue.length ){
				var chNode = tempQueue.shift().childNode;
				while( chNode ){
					if ( chNode.childNode) {
						nodeQueue.push( chNode );
					}
					chNode = chNode.nextNode;
				}
			}
		}		
	}

	function updateNodeLocation(preNode, tmpNode ){
		var mid = Math.floor( tmpNode.childNum / 2) ;
		var child = tmpNode.childNode;
		var midChild = null;
		//find the middle node of the given node
		if ( tmpNode.childNum % 2 == 0 ) {
			for (var i = 0; i < mid - 1; i++) {
				child = child.nextNode;
			}
			midChild = child;
			var childNodeWidth =  child.right - child.left;
			child.right = (tmpNode.right + tmpNode.left)/2 - siblingGap/2;
			child.left = child.right - childNodeWidth ;
		}else{
			for (var i = 0; i < mid ; i++) {
				child = child.nextNode;
			}
			midChild = child;
			var childNodeWidth =  child.right - child.left;
			child.right = (tmpNode.right + tmpNode.left)/2 +  childNodeWidth/2;
			child.left = child.right - childNodeWidth ;
		}		

		// reset the left and right of node again.
		while( child.preNode ){
			child = child.preNode;
			childNodeWidth = child.right - child.left;
			child.right = child.nextNode.left - siblingGap;
			child.left = child.right - childNodeWidth;
		}

		if ( preNode && (preNode.right + siblingGap) > child.left ) {
			childNodeWidth = child.right - child.left;
			child.left = preNode.right + siblingGap + 2;
			child.right = child.left + childNodeWidth;
			child = child.nextNode;

			while( child != midChild.nextNode){
				childNodeWidth = child.right - child.left;
				child.left = child.preNode.right + siblingGap ;
				child.right = child.left + siblingGap;
				child = child.nextNode;
			}
		}

		child = midChild;
		while( child.nextNode ){
			child = child.nextNode;
			childNodeWidth = child.right - child.left;
			child.left = child.preNode.right + siblingGap;
			child.right = child.left + childNodeWidth;
		}
	}

    function DrawParsingTree(nodeTree,nY){
        var pNodeA = nodeTree.begin;
        var bIsFirst = true;
        while( pNodeA != null){
            DrawNodes(pNodeA, nY, 0);
            CurX += gap;
            pNodeA = pNodeA.nextNode;
            bIsFirst = false;
        }
    }

    function DrawNodes(pNode, nY, tempLevel){
        if ( pNode == null ){
            return ;
        }
		tempLevel++;
		level = (tempLevel > level) ? tempLevel:level;
		
        var pNodeA = pNode.childNode;
        DrawOneNode(nY, pNode);
        while( pNodeA != null ){
            var LocalY = nY + pNode.bottom - pNode.top + fontSize * times;
            if( LocalY > Max )
                Max = LocalY;
            DrawNodes(pNodeA, LocalY, tempLevel);
            pNodeA = pNodeA.nextNode;
			
            if(pNodeA != null)
                CurX += (textwidth + 20);
        }
    }

    function DrawOneNode(localY, pNode){
        pNode.left = CurX;
        pNode.top = localY;
        if ( pNode.word == "" )
            return;
		

		var pattern_test = new RegExp(/(\S+)\/D\:(\S+)/);
		if ( pattern_test.test(pNode.word) ) {
			var arr = pattern_test.exec(pNode.word);
			textwidth = context.measureText(arr[1]).width + fontSize*2;
			pNode.right = CurX + textwidth;
			pNode.bottom = localY + fontSize;
		}else{
			textwidth = context.measureText(pNode.word).width + fontSize*2;
			pNode.right = CurX + textwidth;
			pNode.bottom = localY + fontSize;
		}

		if( pNode.right > textWidthMax){
			textWidthMax = pNode.right;
		}
    }
		
    function SetLeafInfo(pNode){
        if ( pNode == null){
            return;
        }
        var pNodeA = pNode.childNode;
        while( pNodeA != null ){
            SetLeafInfo(pNodeA);
            pNodeA = pNodeA.nextNode;
        }

        if (pNode.childNode == null ){
            pNode.isLeaf = true ;
        }
    }

    function GetTreeSent(pNode, pszLeafInfo, bIsDelChar){
        var pNodeA = pNode.childNode;
        if( pNodeA == null){
            var strTmpA = pNode.pos;
            if( strTmpA != ""){
                var strTmpB = strTmpA.substr(strTmpA.indexOf(" "));
                if( bIsDelChar ){
                    if( strTmpB.indexOf("\"") == 0){
                        strTmpB = strTmpB.substr(1);
                    }
                    if(strTmpB.length >0 && strTmpB.indexOf("\"") == strTmpB.length-1){
                        strTmpB = strTmpB.substr(0,strTmpB.length-1);
                    }
                }
                pszLeafInfo[0] += strTmpB;
            }
        }

        while( pNodeA != null){
            GetTreeSent(pNodeA,pszLeafInfo,true);
            pNodeA = pNodeA.nextNode;
        }
    }

    function ParseInput(pNode, txt) {
        var arr = [] ;
        var psTerminal = "";
        var psSonNode = [];
        var nSonNum = 0;
        arr.push(psSonNode);
        arr.push(nSonNum);
        arr.push(psTerminal);

        GetSonNode(txt, arr);
        psTerminal = arr.pop();
        nSonNum = arr.pop();
        psSonNode = arr.pop();

        if (nSonNum != 0) {
            pNode.setPos(psTerminal);
            pNode.setWord(psTerminal);
            pNode.setChildNum(nSonNum);
        } else {
            return;
        }

        pNode.childNode = new NodeStr();
        MakeList(pNode.childNode, psSonNode, nSonNum);
        var psOneNode = ""; 
        var pNodeTmp = null;

        pNodeTmp = pNode.childNode;
        for (var iCnt = 0; iCnt < nSonNum; iCnt++) {
            pNodeTmp.parentNode = pNode;
            psOneNode = psSonNode[iCnt];
            if (psOneNode.lastIndexOf("[") != -1) {
                ParseInput(pNodeTmp, psOneNode);
            } else {
				psOneNode = psOneNode.replace(/\]/,"");
                pNodeTmp.setPos(psOneNode);
                pNodeTmp.setWord(psOneNode);
            }
            pNodeTmp = pNodeTmp.nextNode;
        }
    }

    function GetSonNode(psOneNodeString, arr) {
        var psTerminal = arr[2];
        var nSonNum = arr[1];
        var ppsSonNode = arr[0];

        var sBracket = [], nBracketNum = 0;
        var sAllBracket = [];
        var psOneNodeStringTmp = psOneNodeString;
        var psStart = 0, bIsFirst = true, index = 0;
        var len = psOneNodeStringTmp.length ;
        while( index < len ){
            if( psOneNodeStringTmp[index] == '[' && psTerminal == "" && index > 0) {
                psTerminal = psOneNodeStringTmp.substr(0, index);
            }

            if( psOneNodeStringTmp[index] == '[') {
                sBracket[nBracketNum++] = '[';
                if (bIsFirst) {
                    psStart = index + 1;
                    bIsFirst = false;
                }
            }

            if( psOneNodeStringTmp[index] == ']') {
                if (sBracket[nBracketNum - 1] == '[') {
                    nBracketNum--;
                    if (nBracketNum == 0) {
                        var psTmp = psOneNodeStringTmp.substr(psStart, index - psStart + 1);
                        sAllBracket[nSonNum] = psTmp;
                        bIsFirst = true;
                        nSonNum ++;
                    }
                }
            }
            index++;
        }

        if (nSonNum != 0) {
            var psTmp = "";
            for (var iCnt = 0; iCnt < nSonNum; iCnt++) {
                psTmp = sAllBracket[iCnt]
                ppsSonNode.push(psTmp);
            }
        }

        arr[2] = psTerminal;
        arr[1] = nSonNum;
        arr[0] = ppsSonNode;
    }

    function MakeList(pNode, ppsSonNode, nSonNum) {
        var pNodePre = pNode;
        var pNodeNow = null;
        for (var iCnt = 1; iCnt < nSonNum; iCnt++) {
            pNodeNow = new NodeStr();
            pNodeNow.preNode = pNodePre;
            pNodePre.nextNode = pNodeNow;
            pNodePre = pNodeNow;
        }
    }

    function NodeStr() {
        this.parentNode = null;
        this.childNode = null;
        this.preNode = null;
        this.nextNode = null;

        this.isLeaf = false;
        this.isSelect = false;

        this.pos = "";
        this.word = "";
        this.position = 0;

        this.left = 0;
        this.right = 0
        this.bottom = 0;
        this.top = 0;
        this.childNum = 0;
        
        this.setWord = function (word) {
            this.word = word;
        }

        this.setPos = function (pos) {
            this.pos = pos;
        }

        this.setChildNum = function(num){
        	this.childNum = num;
        }
    }

    function Tree() {
        this.begin = null;
        this.end = null;

        this.curPos = 0;
        this.treeSize = 0;
    }