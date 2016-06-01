<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Draw Tree</title>
    <style type="text/css">
    	#textInput{
    		display: inline-block;
    		width: 90%;
    		font-size: 1.5em;
    	}

		button.treeSet{
			display: inline-block;
			transform: translate(0, -70%);
		}

    	button{
    		border-radius: 10px;
    		border-width: 1px;
    		outline: none;
    		background-color: transparent;
    		font-size: 30px;
    		border-style: solid;
    	}

    	.changeWord{
    		font-size: 20px;
    	}

		#treeContainer{
			position: relative;
			margin-top: 20px;
		}

		#tree{
			margin-top: 40px;
		}

		div.edit{
			position: absolute;
			margin-top: 5px;
			right: calc(50% - 122px);
		}

    	#range{
    		position: absolute;
    		left: calc(50% + 140px);
    		top: 5px;
    	}

		#range::before {
			display: block;
			position: absolute;
			left: -10px;
			height: 21px;
			line-height: 21px;
			font-size: 1.5em;
			color: black;
			content: attr(min);
		}

		#range::after {
			display: block;
			position: absolute;
			right: -25px;
			height: 21px;
			line-height: 21px;
			font-size: 1.5em;
			color: black;
			content: attr(max);
		}

		div.checkContainer{
			position: absolute;
			margin-top: 5px;
			font-size: 1.2em;
			widows: 200px;
			left: calc(50% + 140px);
			top: 30px;
		}

		button.setWord{
			display: none;
		}
    </style>
</head>
<body style="text-align:center;">
	<textarea id="pattern" style="display: none;">
	,@#(新华社
	</textarea>
	<div class="treeInputContainer">
		<textarea id="textInput">[S[NP[German/D:S][chancellor/D:S]][VP[opens/D:S][NP[film/D:S][festival/D:S]]]]</textarea>
		<button class="treeSet">Tree</button>
	</div>
	<br/>
	<button disabled class ="undo" >undo</button>
	<button disabled class ="redo" >redo</button>
	<button disabled class ="delete" >delete</button>
	<button disabled class ="add" >add</button>
	<button disabled class ="combine" >combine</button>
	<button disabled class ="movedown" >movedown</button>
	<button disabled class ="moveup" >moveup</button>

	<div id="treeContainer" class="canvasContainer">
		<div class="edit">
			<input class="changeWord"></input>
			<button class="setWord">OK</button>
		</div>
		<input type="range" id="range" min="1" max="40" step="1" name="range" value="16" onchange="onRangeChange(event)"/>
		<div class="checkContainer">
			<label>dependent tree</label>
			<input type="checkbox" class="checkbox"></input>
		</div>
			<br/>

	    <canvas id="tree" width="800" height="600">
	        Your browser dose not support the  canvas tag.
	    </canvas>
	</div>

<script type="text/javascript">
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
		ch1.x = cep.x + l * (this.sharp || 0.075);
		ch1.y = cep.y - l * (this.size  || 0.075);
		ch2.x = cep.x - l * (this.sharp || 0.075);
		ch2.y = cep.y - l * (this.size  || 0.075);
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
</script>

<script type="text/javascript">
    var canvasWidth = 800, canvasHeight = 600,
    textWidthMax = 0,  // the whole width of the word text will be drawn.
    redo = document.getElementsByClassName("redo")[0],
    undo = document.getElementsByClassName("undo")[0],
    dele = document.getElementsByClassName("delete")[0],
    add  = document.getElementsByClassName("add")[0],
    combine  = document.getElementsByClassName("combine")[0],
    moveup = document.getElementsByClassName("moveup")[0],
    movedown = document.getElementsByClassName("movedown")[0],
    changeWord = document.getElementsByClassName("changeWord")[0],
    setWord = document.getElementsByClassName("setWord")[0],
    treeSet = document.getElementsByClassName('treeSet')[0],
    checkbox = document.getElementsByClassName('checkbox')[0],
    textInput = document.getElementById('textInput'),

    canvas, context, Max = 10, CurX = 10, CurY = 10,
    fontGap = 3, fontSize = 10, siblingGap = 20, level = 0, gap = 12,
    nodeWidth = 0,   interlHeight = 0,	//layer height
    times = 6, nodeTree = null, textwidth = 0, 	//word text width
	position = 0,	pattern="", //which pattern will be highlight
	pszLeafInfo = [""], leavesData = "", isCtrl = false, leaves = [],
	treeJson={}, undoArr=[], redoArr=[], selNodes = [], curSelNode = null,
	prePosition = 0, postPosition = 0, cursor = 0,
	preNode = null,//prenode is the prenode of the relocation and is the pre of selNodes
	nextNode = null, treeAction = null;//treeAction means the actually handle of the tree

    window.onload = function(){
		pattern = document.getElementById("pattern").value.trim().split("@#");
		fontSize = parseInt(document.getElementById('range').value);
		init();

		canvas.addEventListener("click", canvasClick);

		document.addEventListener('keydown', function(e){
			switch (e.keyCode) {
			    case 17:
			    	isCtrl = true;
			    	break;
			    default:
			    	return;
			}
		});

		document.addEventListener('keyup', function(e){
			isCtrl = false;
		});

		add.addEventListener('click', selTreeNewParentAssign);
		dele.addEventListener('click',selTreeDeleteOneNodeAssign);
		combine.addEventListener('click',selTreeCombineAssign);
		moveup.addEventListener('click',selTreeMoveUpAssign);
		movedown.addEventListener('click',selTreeMoveDownAssign);
		undo.addEventListener('click',selTreeUndo);
		redo.addEventListener('click',selTreeRedo);
		setWord.addEventListener('click',selTreeModify);
		treeSet.addEventListener('click',resetTree);
		checkbox.addEventListener('click', changeState);
		changeWord.addEventListener('keydown', setNodeWord);
		onClick();
    }

    function setNodeWord(e){
    	if (e.which == 13) {
    		selTreeModify();
    		changeWord.blur();
    	}
    }

    function canvasClick(e){
		var rect = canvas.getBoundingClientRect();
		var mousePos = {x:e.clientX - rect.left, y:e.clientY - rect.top};
		curSelNode = null;
		if ( isCtrl ) {
			console.log("control");
		}else{
			clearSelNodes(nodeTree.begin);
			curSelNode = null;
		}
		hitDetect(nodeTree.begin, mousePos);
		reDrawTree();
		checkTools();
    }

    function changeState(e){
    	if(e.target.checked){
			add.setAttribute('disabled','true');
			dele.setAttribute('disabled','true');
			combine.setAttribute('disabled','true');
			moveup.setAttribute('disabled','true');
			movedown.setAttribute('disabled','true');
			undo.setAttribute('disabled','true');
			redo.setAttribute('disabled','true');
			setWord.setAttribute('disabled','true');
			treeSet.setAttribute('disabled','true');
			changeWord.setAttribute('disabled','true');
			textInput.setAttribute('disabled','true');
			canvas.removeEventListener('click', canvasClick);
			reDrawTree();
    	}else{
			setWord.removeAttribute('disabled');
			treeSet.removeAttribute('disabled');
			changeWord.removeAttribute('disabled');
			textInput.removeAttribute('disabled');
			canvas.addEventListener("click", canvasClick);
			reDrawTree();
			check4Undo();
			check4Redo();
    	}
    }

    function init(){
    	initTreeData();
    	initCanvas();
    }

    function initTreeData(){
		var txt = document.getElementById("textInput").value.trim();
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
		fontSize = parseInt(evt.target.value);
		reDrawTree();
	}

	function reDrawTree(){
		cursor = 0;

		setCanvasBoundry();			//set the boundary of the canvas
		initCanvasContext(); 		//set the context font and curX
        DrawParsingTree(nodeTree,5);
        leaves = [];
        SetLeafInfo(nodeTree.begin);

		reInitcancas();
		initCanvasContext();
		reLocation( nodeTree.begin );
		updateTreeLocation2(nodeTree.begin);
		moveTree2Center(nodeTree.begin, canvasWidth/2);
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
		moveTree2Center(nodeTree.begin, canvasWidth/2);
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
		var state = checkbox.checked;
		if (state) {
			drwaTreeWithDepentant(pNode);
			return;
		}

		var nodeQueue = [];
		nodeQueue.push( pNode );
		while( nodeQueue.length ){
			var tmpNode = nodeQueue.shift();
			if (tmpNode.isSelect) {
				reDrawOneNodeDetect(tmpNode);
			}else{
				reDrawOneNode(tmpNode);
			}

			var chNode = tmpNode.childNode;
			while( chNode ){
				reDrawLine(tmpNode, chNode);
				nodeQueue.push( chNode );
				chNode = chNode.nextNode;
			}
		}
	}

	function drwaTreeWithDepentant(pNode){
		var nodeQueue = [];
		nodeQueue.push( pNode );
		while( nodeQueue.length ){
			var tmpNode = nodeQueue.shift();
			reDrawOneNodeWithDepentant(tmpNode);

			var chNode = tmpNode.childNode;
			var pattern_test = new RegExp(/(\S+)\/([DU])\:(\S+)/);
			while( chNode ){
				if ( pattern_test.test(chNode.word) ) {
					var arr = pattern_test.exec(chNode.word);
					if (arr[2] == 'D') {
						reDrawLineDown(tmpNode, chNode);
					}else if (arr[2] == 'U'){
						reDrawLineUp(chNode, tmpNode);
					}
				}else{
					reDrawLine(tmpNode, chNode);
				}
				nodeQueue.push( chNode );
				chNode = chNode.nextNode;
			}
		}
	}

	function reDrawLine(pNodeA, pNodeB){
        context.beginPath();
        context.strokeStyle = "blue";
        context.moveTo( (pNodeA.right + pNodeA.left)/2, pNodeA.bottom+6);
        context.lineTo( (pNodeB.right + pNodeB.left)/2, pNodeB.top);
        context.stroke();
        context.closePath();
	}

	function reDrawLineDown(pNodeA, pNodeB){
        context.beginPath();
        context.strokeStyle = "blue";
        var sp = {x:(pNodeA.right+pNodeA.left)/2,
        		  y:pNodeA.bottom+6};
       	var ep = {x:(pNodeB.right+pNodeB.left)/2,
       			  y:pNodeB.top}

	    var arrow = new Arrow(sp,ep,{});
	    arrow.paint(context);
	}

	function reDrawLineUp(pNodeA, pNodeB){
        context.beginPath();
        context.strokeStyle = "blue";
        var sp = {x:(pNodeA.right+pNodeA.left)/2,
        		  y:pNodeA.top };
       	var ep = {x:(pNodeB.right+pNodeB.left)/2,
       			  y:pNodeB.bottom + 6
       	}
	    var arrow = new Arrow(sp,ep,{});
	    arrow.paint(context);
	}

	function reDrawOneNodeWithDepentant(pNode){
		var pattern_test = new RegExp(/(\S+)\/[DU]\:(\S+)/);
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

			context.beginPath();
        	context.strokeStyle = "black";
			context.moveTo(pNode.left , pNode.top + fontGap);
			context.arcTo(pNode.left, pNode.top, pNode.left+fontGap, pNode.top , fontGap );//left top corner
			context.lineTo(pNode.right - fontGap, pNode.top);
			context.arcTo(pNode.right, pNode.top , pNode.right, pNode.top + fontGap, fontGap );//right top corner
			context.lineTo(pNode.right, pNode.bottom + fontGap);
			context.arcTo(pNode.right, pNode.bottom + fontGap*2, pNode.right - fontGap, pNode.bottom + fontGap*2, fontGap );//right bottom corner
			context.lineTo(pNode.left + fontGap, pNode.bottom + fontGap *2);
			context.arcTo(pNode.left , pNode.bottom  + fontGap *2, pNode.left, pNode.bottom , fontGap );//left bottom corner
			context.lineTo(pNode.left, pNode.top + fontGap);
			context.stroke();

			context.textAlign="start";
			context.fillText(arr[2], (sp.x + ep.x)/2, (sp.y + ep.y)/2,  context.measureText(arr[2]).width);

			context.textAlign="center";
			context.fillText(arr[1], (pNode.left + pNode.right)/2, pNode.bottom + fontGap/2, textwidth);
		}else{
			context.beginPath();
        	context.strokeStyle = "black";
			context.moveTo(pNode.left , pNode.top + fontGap);
			context.arcTo(pNode.left, pNode.top, pNode.left+fontGap, pNode.top , fontGap );//left top corner
			context.lineTo(pNode.right - fontGap, pNode.top);
			context.arcTo(pNode.right, pNode.top , pNode.right, pNode.top + fontGap, fontGap );//right top corner
			context.lineTo(pNode.right, pNode.bottom + fontGap);
			context.arcTo(pNode.right, pNode.bottom + fontGap*2, pNode.right - fontGap, pNode.bottom + fontGap*2, fontGap );//right bottom corner
			context.lineTo(pNode.left + fontGap, pNode.bottom + fontGap *2);
			context.arcTo(pNode.left , pNode.bottom  + fontGap *2, pNode.left, pNode.bottom , fontGap );//left bottom corner
			context.lineTo(pNode.left, pNode.top + fontGap);
			context.stroke();

			context.textAlign="center";
			context.fillText(pNode.word, (pNode.left + pNode.right)/2, pNode.bottom + fontGap/2, textwidth);
		}
	}

	function reDrawOneNode( pNode ){
		context.beginPath();
        context.strokeStyle = "black";
		context.moveTo(pNode.left , pNode.top + fontGap);
		context.arcTo(pNode.left, pNode.top, pNode.left+fontGap, pNode.top , fontGap );//left top corner
		context.lineTo(pNode.right - fontGap, pNode.top);
		context.arcTo(pNode.right, pNode.top , pNode.right, pNode.top + fontGap, fontGap );//right top corner
		context.lineTo(pNode.right, pNode.bottom + fontGap);
		context.arcTo(pNode.right, pNode.bottom + fontGap*2, pNode.right - fontGap, pNode.bottom + fontGap*2, fontGap );//right bottom corner
		context.lineTo(pNode.left + fontGap, pNode.bottom + fontGap *2);
		context.arcTo(pNode.left , pNode.bottom  + fontGap *2, pNode.left, pNode.bottom , fontGap );//left bottom corner
		context.lineTo(pNode.left, pNode.top + fontGap);
		context.stroke();

		context.textAlign="center";
		context.fillStyle = "black";
		context.fillText(pNode.word, (pNode.left + pNode.right)/2, pNode.bottom + fontGap/2);
	}

	function reDrawOneNodeDetect(pNode){
		context.beginPath();
        context.strokeStyle = "black";
		context.moveTo(pNode.left , pNode.bottom + fontGap*2);
		context.lineTo(pNode.right, pNode.bottom + fontGap*2);
		context.stroke();

		context.textAlign="center";
		context.fillStyle = "red";
		context.fillText(pNode.word, (pNode.left + pNode.right)/2, pNode.bottom + fontGap/2);
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
		preNode = null;
		var tempQueue = [];
		while( nodeQueue.length ){
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
				child.right = child.left + childNodeWidth;
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

	function moveTree2Center(pNode, offset){
		var nodeQueue = [];
		nodeQueue.push(pNode);
		var widthGap = offset - pNode.left - (pNode.right - pNode.left)/ 2;
		while( nodeQueue.length ){
			var tmpNode = nodeQueue.shift();
			tmpNode.left += widthGap;
			tmpNode.right+= widthGap;

			var childNode = tmpNode.childNode;
			while(childNode){
				nodeQueue.push(childNode);
				childNode = childNode.nextNode;
			}
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

		textwidth = context.measureText(pNode.word).width + fontSize*2;
		pNode.right = CurX + textwidth;
		pNode.bottom = localY + fontSize;

		if( pNode.right > textWidthMax){
			textWidthMax = pNode.right;
		}
    }

    function SetLeafInfo(pNode){
        if ( pNode == null){
            return;
        }

        var pNodeA = pNode.childNode;
        var num = 0;
        while( pNodeA != null ){
            SetLeafInfo(pNodeA);
            pNodeA = pNodeA.nextNode;
            num ++;
        }
        pNode.childNum = num;

        if (pNode.childNode == null ){
            pNode.isLeaf = true ;
            pNode.childNum = 0;
            leaves.push(pNode);
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

    function saveTree2JSON() {
    	var word = nodeTree.begin.word;
		treeJson[word] = [];
		tree2json(nodeTree.begin,treeJson[word]);
		var txt = JSON.stringify(treeJson);
		undoArr.push(txt);
    }

    function selTreeCombineAssign(argument) {
    	treeAction = selTreeCombine;
    	treeEdit();
    }

    function selTreeCombine() {
    	var nodeA = selNodes[0];
    	var nodeB = selNodes[1];

    	var strTmp = nodeA.word + " " + nodeB.word;
    	nodeA.setWord(strTmp);
    	curSelNode = nodeA;
    	nodeA.isSelect = true;
    	selNodes = [];
    	if (nodeB.preNode == null) {
    		if (nodeB.nextNode == null) {
    			nodeB.parentNode.childNode = null;
    		}else{
    			nodeB.parentNode.childNode = nodeB.nextNode;
    			nodeB.nextNode.preNode = null;
    		}
    	}else{
    		if(nodeB.nextNode == null){
    			nodeB.preNode.nextNode = null;
    		}else{
    			nodeB.preNode.nextNode = nodeB.nextNode;
    			nodeB.nextNode.preNode = nodeB.preNode;
    		}
    	}
    }

    function selTreeModifyAssign(e){
    	treeAction = selTreeModify;
    	treeEdit();
    }

    function selTreeModify() {
    	if (!curSelNode) {
    		return;
    	}

    	saveTree2JSON();
    	if ( curSelNode ) {
    		curSelNode.setWord( changeWord.value);
    	}

    	reDrawTree();
    	tree2String();
    	check4Undo();
    	check4Redo();
    }

    function selTreeNewParentAssign(e){
		treeAction = selTreeNewParent;
		treeEdit();
    }

    function selTreeNewParent() {
    	var nodeA = selNodes[0];
    	var nodeB = selNodes[selNodes.length - 1];

    	var newNode = new NodeStr();
    	newNode.setWord("Fill it!");
    	curSelNode = newNode;

    	newNode.parentNode = nodeA.parentNode;
    	newNode.childNode = nodeA;
    	newNode.preNode = nodeA.preNode;
    	newNode.nextNode = nodeB.nextNode;

    	if (nodeA.preNode == null) {
    		nodeA.parentNode.childNode = newNode;
    	}else{
    		nodeA.preNode.nextNode = newNode;
    	}

    	nodeA.preNode = null;
    	nodeB.nextNode = null;
    	for (var i = 0; i < selNodes.length; i++) {
    		selNodes[i].parentNode = newNode;
    	}

    	clearSelNodes(nodeTree.begin);
    	curSelNode.isSelect = true;
    	selNodes.push(curSelNode);
    	changeWord.value = curSelNode.word;
    	changeWord.focus();
    }

    function selTreeDeleteOneNodeAssign(argument){
		treeAction = selTreeDeleteOneNode;
		treeEdit();
    }

    function selTreeDeleteOneNode() {
    	var nodeA = selNodes[0];
    	if (nodeA.preNode == null) {
    		if (nodeA.nextNode == null) {
    			nodeA.parentNode.childNode = nodeA.childNode;
    			var nodeB = nodeA.childNode;
    			while(nodeB){
    				nodeB.parentNode = nodeA.parentNode;
    				nodeB = nodeB.nextNode;
    			}
    		}else{
    			nodeA.parentNode.childNode = nodeA.childNode;
    			if ( nodeA.childNode ) {
    				var nodeB = nodeA.childNode;
    				var nodeC = null;

    				while( nodeB){
    					nodeB.parentNode = nodeA.parentNode;
    					nodeC = nodeB;
    					nodeB = nodeB.nextNode;
    				}
    				nodeC.nextNode = nodeA.nextNode;
    				nodeA.nextNode.preNode = nodeC;
    			}
    		}
    	}else{
    		if (nodeA.nextNode == null) {
    			nodeA.preNode.nextNode = nodeA.childNode;
    			nodeA.childNode.preNode = nodeA.preNode;

    			var nodeB = nodeA.childNode;
    			while(nodeB){
    				nodeB.parentNode = nodeA.parentNode;
    				nodeB = nodeB.nextNode;
    			}
    		}else{
    			if ( nodeA.childNode ) {
    				nodeA.preNode.nextNode = nodeA.childNode;
    				nodeA.childNode.preNode = nodeA.preNode;

    				var nodeB = nodeA.childNode;
    				var nodeC = null;
    				while(nodeB){
    					nodeB.parentNode = nodeA.parentNode;
    					nodeC = nodeB;
    					nodeB = nodeB.nextNode;
    				}
    				nodeC.nextNode = nodeA.nextNode;
    				nodeA.nextNode.preNode = nodeC;
    			}else{
    				nodeA.preNode.nextNode = nodeA.nextNode;
    				nodeA.nextNode.preNode = nodeA.preNode;
    			}
    		}
    	}

        clearSelNodes(nodeTree.begin);
    	curSelNode = null;
    }

    function selTreeMoveUpAssign(argument){
		treeAction = selTreeMoveUp;
		treeEdit();
    }

    function selTreeMoveUp() {
    	var nodeA = selNodes[0];
    	var nodeB = selNodes[selNodes.length - 1];

    	if (nodeA.preNode == null) {
    		nodeA.parentNode.childNode = null;
    	}else{
    		nodeA.preNode.nextNode = null;
    	}

    	var nodeC = null;
    	var nodeD = nodeA.parentNode.parentNode;

    	var firstParent = nodeA.parentNode;
    	var endParent = nodeA.parentNode.nextNode;

    	firstParent.nextNode = nodeA;
    	nodeA.preNode = firstParent;

    	if (endParent) {
    		endParent.preNode = nodeB;
    		nodeB.nextNode = endParent;
    	}

    	for (var i = 0; i < selNodes.length; i++) {
    		nodeC =  selNodes[i];
    		nodeC.parentNode = nodeD;
    	}
    }

	function selTreeMoveDownAssign(argument){
		treeAction = selTreeMoveDown;
		treeEdit();
	}

    function selTreeMoveDown() {
    	var nodeA = selNodes[0];
    	var nodeB = selNodes[selNodes.length - 1];
    	var nodeC = nodeA.preNode;
    	var nodeD = null;

    	for (var i = 0; i < selNodes.length; i++) {
    		nodeD = selNodes[i];
    		nodeD.parentNode = nodeC;
    	}

    	nodeA.preNode.nextNode = nodeB.nextNode;
    	if (nodeB.nextNode) {
    		nodeB.nextNode.preNode = nodeA.preNode;
    	}

    	nodeB.nextNode = null;
    	var nodeE = null;
    	nodeC = nodeC.childNode;
    	while(nodeC){
    		nodeE = nodeC;
    		nodeC = nodeC.nextNode;
    	}

    	nodeE.nextNode = nodeA;
    	nodeA.preNode = nodeE;
    }

    function selTreeUndo(argument) {
    	var word = nodeTree.begin.word;
		treeJson[word] = [];
		tree2json(nodeTree.begin,treeJson[word]);
		var txt = JSON.stringify(treeJson);
		redoArr.push(txt);

    	var text = undoArr.pop();
    	var tmptree = JSON.parse(text);
		var word = Object.keys(tmptree);
		nodeTree.begin.word = word[0];
		nodeTree.begin.pos = word[0];
		nodeTree.begin.childNode = null;
		json2tree( nodeTree.begin, tmptree[word[0]] );

		reDrawTree();
		tree2String();
    	check4Redo();
    	check4Undo();
    }

    function selTreeRedo(argument) {
    	var word = nodeTree.begin.word;
		treeJson[word] = [];
		tree2json(nodeTree.begin,treeJson[word]);
		var txt = JSON.stringify(treeJson);
		undoArr.push(txt);

    	var text = redoArr.pop();
    	var tmptree = JSON.parse(text);
		var word = Object.keys(tmptree);
		nodeTree.begin.word = word[0];
		nodeTree.begin.pos = word[0];
		nodeTree.begin.childNode = null;
		json2tree( nodeTree.begin, tmptree[word[0]] );

		reDrawTree();
		tree2String();
    	check4Redo();
    	check4Undo();
    }

    function isSameLayerAndNeighbor() {
    	if (selNodes.length < 2) {
    		return false;
    	}

    	var parent = selNodes[0].parentNode;
    	var child = parent.childNode;
    	var tmpNodes = [];
    	while(child){
    		for (var i = 0; i < selNodes.length; i++) {
    			if(selNodes[i] == child){
    				tmpNodes.push(child);
    			}
    			
    			// if(selNodes[i].word == child.word){
    			// 	tmpNodes.push(child);
    			// }
    		}
    		child = child.nextNode;
    	}

    	if (tmpNodes.length != selNodes.length) {
    		return false;
    	}

    	preNode = tmpNodes[0];
    	nextNode = tmpNodes[tmpNodes.length -1];
    	selNodes = tmpNodes;
    	for (var i = 1; i < tmpNodes.length; i++) {
    		if(tmpNodes[i].preNode != tmpNodes[i-1] ){
    			return false;
    		}
    	}

    	return true;
    }

    function isNeighborLeaves() {
    	if (selNodes.length != 2) {
    		return false;
    	}
    	
    	var k = 0, j = 0;
    	var tmpNodes = [];
    	for (var i = 0; i < leaves.length; i++) {
    		if (leaves[i].word == selNodes[0].word && !selNodes[0].childNode){
    			k = i;
    			tmpNodes.push(leaves[i]);
    		}

    		if (leaves[i].word == selNodes[1].word && !selNodes[1].childNode) {
    			j = i;
    			tmpNodes.push(leaves[i]);
    		}
    	}

    	if (selNodes.length != tmpNodes.length) {
    		return false;
    	}

    	selNodes = tmpNodes;
    	if ( k - j == 1 || j - k == 1) {
    		return true;
    	}

    	return false;
    }

    function check4Del() {
    	dele.setAttribute('disabled',"true");
    	if ( selNodes.length == 1 && selNodes[0].parentNode && selNodes[0].childNode ) {
    		dele.removeAttribute('disabled');
    	}
    }

    function check4Add() {
		add.setAttribute('disabled',"true");
		if (selNodes.length == 1 && selNodes[0].parentNode) {
			add.removeAttribute('disabled');
		}

    	if (isSameLayerAndNeighbor()){
    		add.removeAttribute('disabled');
    	}
    }

    function check4Combine() {
    	combine.setAttribute('disabled',"true");
    	if ( isNeighborLeaves() ){
    		combine.removeAttribute('disabled');
    	}
    }

    function check4Down() {
    	movedown.setAttribute('disabled',"true");

    	if ( !selNodes.length) {
    		return;
    	}

    	if (selNodes.length == 1 && selNodes[0].preNode && selNodes[0].preNode.childNode) {
    		movedown.removeAttribute('disabled');
    	}

    	if (isSameLayerAndNeighbor() && preNode.preNode && preNode.preNode.childNode){
    		movedown.removeAttribute('disabled');
    	}

    	var nodeA = selNodes[0];
    	var nodeB = null;

    	var nodeC = nodeA.preNode;
    	if ( nodeC == null || nodeC.childNode == null) {
    		movedown.setAttribute('disabled',"true");
    	}

    	if (nodeA.childNode == null) {
    		movedown.setAttribute('disabled',"true");
    	}
    }

    function check4Up() {
    	moveup.setAttribute('disabled',"true");

    	if ( !selNodes.length) {
    		return;
    	}

    	if (selNodes.length == 1 && selNodes[0].preNode && !selNodes[0].nextNode && !selNodes[0].childNode) {
    		moveup.removeAttribute('disabled');
    	}

    	if ( isSameLayerAndNeighbor() && preNode.preNode && !nextNode.nextNode && !preNode.childNode) {
    		moveup.removeAttribute('disabled');
    	}

    	var nodeA = selNodes[0];
    	var nodeB = selNodes[selNodes.length - 1];
    	if (nodeB.nextNode) {
    		moveup.setAttribute('disabled',"true");
    	}

    	if (nodeA.parentNode == null || nodeA.parentNode.parentNode == null) {
    		moveup.setAttribute('disabled',"true");
    	}
    }

    function check4Redo() {
    	redo.setAttribute('disabled',"true");
    	if (redoArr.length) {
    		redo.removeAttribute('disabled');
    	}
    }

    function check4Undo() {
    	undo.setAttribute('disabled','true');
    	if (undoArr.length) {
    		undo.removeAttribute('disabled');
    	}
    }

    function checkTools() {
    	check4Del();
    	check4Add();
    	check4Combine();
    	check4Down();
    	check4Up();
    	check4Undo();
    }

    function treeEdit(){
    	saveTree2JSON();

	 	treeAction();

    	reDrawTree();
    	tree2String();
    	check4Undo();
    	check4Redo();
    	checkTools();
    }

    function clearSelNodes(pNode) {//first root travel
    	var nodeArr = [];
    	nodeArr.push( pNode );
    	while( nodeArr.length ){
    		var tmpNode = nodeArr.shift();
    		tmpNode.isSelect = false;

	    	var childNode = tmpNode.childNode;
	    	while( childNode ){
				nodeArr.push(childNode);
	    		childNode = childNode.nextNode;
	    	}
    	}

    	selNodes = [];
    }

    function hitDetect(pNode, mousePos) {
    	var nodeArr = [];
    	nodeArr.push(pNode);
    	while( nodeArr.length ){
    		var tmpNode = nodeArr.shift();
	    	if ( tmpNode.pointInNode(mousePos) ) {
	    		tmpNode.isSelect = true;
	    		curSelNode = tmpNode;
	    		selNodes.push(tmpNode);
	    		changeWord.value = tmpNode.word;
	    		changeWord.focus();
    			console.log(selNodes.length);
	    		return ;
	    	}

	    	var childNode = tmpNode.childNode;
	    	while( childNode ){
	    		nodeArr.push(childNode);
	    		childNode = childNode.nextNode;
	    	}
    	}
    }

    function json2tree(node, json) {
    	var pre = null;
    	for (var i = 0; i < json.length; i++) {
    		var nextNode = new NodeStr();
    		nextNode.parentNode = node;
    		if (pre == null){
    			node.childNode = nextNode;
    		}

    		if( typeof json[i] !== 'string'){
				var word = Object.keys(json[i]);
				nextNode.word = word[0];
				nextNode.pos = word[0];
				nextNode.preNode = pre;
				if ( pre != null) {
					pre.nextNode = nextNode;
				}
				pre = nextNode;
				json2tree(nextNode, json[i][word[0]]);
    		}else{
				nextNode.word = json[i];
				nextNode.pos = json[i];
				nextNode.preNode = pre;
				if ( pre != null) {
					pre.nextNode = nextNode;
				}
				pre = nextNode;
    		}
    	}
    	node.childNum = json.length
    }

    function tree2json(node, json){
    	if ( node.childNode ) {
    		var child = node.childNode;
    		while( child ){
    			var word = child.word;
    			if ( child.childNode ) {
    				var tmpJSON = {};
    				tmpJSON[word] = [];
					json.push( tmpJSON );
    			}else{
    				json.push(word);
    			}

    			var len = json.length;
    			tree2json(child, json[len - 1][word]);
    			child = child.nextNode;
    		}
    	}
    }

    function tree2String(){
    	var str = tree2StringRecursively(nodeTree.begin);
    	document.getElementById("textInput").value = str;
    }

    function tree2StringRecursively(node){
    	if ( !node ) {
    		return '';
    	}else{
    		var childNode = node.childNode;
    		var childStr = '';
    		while(childNode){
    			childStr += tree2StringRecursively(childNode);
    			childNode = childNode.nextNode;
    		}
    		return '[' + node.word + childStr +']';
    	}
    }

    function resetTree(e){
		init();
		onClick();
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
        this.index = 0;

        this.setWord = function (word) {
            this.word = word;
        }

        this.setPos = function (pos) {
            this.pos = pos;
        }

        this.setChildNum = function(num){
        	this.childNum = num;
        }

        this.pointInNode = function (mousePos) {
        	var flag = false;
        	if (  mousePos.x > this.left  && mousePos.x < this.right
        	   && mousePos.y > this.top && mousePos.y < this.bottom + 6) {
        		flag = true;
        	}
        	return flag;
        }
    }

    function Tree() {
        this.begin = null;
        this.end = null;

        this.curPos = 0;
        this.treeSize = 0;
    }

</script>
</body>
</html>
