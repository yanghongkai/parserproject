@extends('help')

@section('content')
<div class="maincontentEx">
<body>
<h1  align="center">JParser说明</h1><p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<h3>使用IDE</h3>
<p>JParser IDE包括两个功能：一是在线调试，另外是知识库管理。</p>
<p>在线调试主是指可以输入汉语句子，查看JParser核心数据结构Lattice，包括其中Lattice中单元内容，以掌握当前JParser的工作状态，即分词情况、属性情况、短语合并情况和边界信息等等。在线调试是IDE的默认首页，也可以通过点击“JParser”进入该功能界面。
<p>知识库管理分为词典库管理和规则库管理两个部分，在JParser IDE中，分别由“词典”和“规则”管理，词典按照词条查询，可以编辑一个词条下词典知识项，规则按照规则名称查询，可以编辑一个规格名下的规则知识项，两个管理功能都包括“增、删、改”，一旦完成编辑，IDE会自动更新JParser，使用最新的内容。如下图1、图2、图3所示。 </p>
<p>&nbsp;</p>
<p align="center"><img src="{{ asset('/pic/clip_image002.jpg')}}" alt="1" width="554" height="338" /> <br />
  图1. JParser  ID词典库管理 </p>
<p align="center">&nbsp;</p>
<p align="center"><img src="{{ asset('/pic/clip_image004.jpg')}}" alt="2" width="554" height="422" /> <br />
  图2.JParser  IDE规则库管理 </p>
<h1>&nbsp;</h1>
<p align="center"><img src="{{ asset('/pic/clip_image006.jpg')}}" alt="3" width="486" height="360" /> </p>
<p align="center">图3.JParser  IDE调试界面 </p>
<p><br />
  可以通过两种方式使用JParser的在线调试功能，一种是按照完成的句法分析任务，选择分析路径上的其中一个状态；另外是一种方式是指定执行某个规则库，该方法是在“分析路径“后面直接输入规则库名称，采用这种方式，调试者可以在规则知识管理界面建立和维护自己的规则集，见图2所示。 <br />
  图3是运行界面，其中包括分析的内部Lattice结构，可以选择单元查看分析结果。这些分析单元对应分词和短语识别的结果，在这些分析单元中： </p>
<p>表一：JParser IDE单元类型 </p>
<table border="1" cellspacing="0" cellpadding="0">
  <tr>
    <td width="153" valign="top"><br />
      序号 </td>
    <td width="177" valign="top"><p>颜色 </p></td>
    <td width="361" valign="top"><p>功能 </p></td>
  </tr>
  <tr>
    <td width="153" valign="top"><p>1</p></td>
    <td width="177" valign="top"><p>绿色 </p></td>
    <td width="361" valign="top"><p>词匹配单元 </p></td>
  </tr>
  <tr>
    <td width="153" valign="top"><p>2</p></td>
    <td width="177" valign="top"><p>深蓝色 </p></td>
    <td width="361" valign="top"><p>属性单元（Disable） </p></td>
  </tr>
  <tr>
    <td width="153" valign="top"><p>3</p></td>
    <td width="177" valign="top"><p>蓝色 </p></td>
    <td width="361" valign="top"><p>属性单元（Enable） </p></td>
  </tr>
  <tr>
    <td width="153" valign="top"><p>4</p></td>
    <td width="177" valign="top"><p>橘黄 </p></td>
    <td width="361" valign="top"><p>合并单元 </p></td>
  </tr>
</table>
<p>&nbsp;</p>
<p align="center"><img src="{{ asset('/pic/clip_image008.jpg')}}" alt="4" width="361" height="263" /> <br />
  图4. 查看合并单元内容 </p>
<p>在查看单元内容时，特别注意合并单元调用的规则信息（RuleInfo），儿子单元（Child）、核心单元（Head）、合并单元属性（POS）等信息。 </p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<h3>知识库格式 </h3>
JParser知识库主要包括两种类型，一种是词典库，另一种是规则库。词典库是单词及对应的词条内容构成；规则是由规则名及对应的规则集构成。
<p><strong>定义1 ：</strong>词条</p>
词条知识描述了单词的语法或者语义属性、词汇搭配知识、分词歧义处理知识等等，是JParser的核心知识源，在IDE中的词典界面，处理的对象就是一词条内容，一个词条的形式： </p>
<p>#POS1<br />
  ^Tree1<br />
  RuleSet1<br />
  #POS2<br />
  ^Tree2<br />
  RuleSet2<br />
  …</p>
<p>这里： <br />
  POSi 词条的词性 <br />
  Treei 词条POSi下的树结构 <br />
  RuleSeti 词条POSi下的规则内容 </p>
<p>注意： </p>
<ol>
  <li>一个词条下按照词性有多套规则集，一个规则名下具有一套规则集。 </li>
  <li>词条词性以“#”开始，POS后加”_ID”，ID是以是唯一号。 </li>
  <li>词条的树以“^” </li>
</ol>
<p>&nbsp;</p>
<p><strong>定义2 ：</strong>规则集与规则</p>
<p>JParser规则库是由多套规则集构成的，每套规则集有唯一的规则集名称，完成特定的词汇或者句法功能。在IDE中，规则库界面，处理的对象就是一个规则集RuleSet<br />
 一套规则集，包括一个或者多个规则，可以包括子规则，形如： </p>
<p>.RuleName1 (Item1 Item2 Item3 … {Operation}  )<br />
  .RuleName2 (Item1 Item2 Item3 … {Operation}  )<br />
  .RuleName3 (Item1 Item2 Item3 … {Operation}  )<br />
  这里： <br />
  RuleName 规则名称 <br />
  Item1 Item2 Item3 规则上下文，由多个规则项Item构成，之间用空格隔离 <br />
  Operation 规则执行的操作 <br />
  注意：规则名RuleName前加“.”，RuleName不能包括空格 </p>
<p>&nbsp;</p>
<p>例如： <br />
    <img src="{{ asset('/pic/clip_image010.jpg')}}" alt="5" width="564" height="155" /> </p>
<p><strong>定义3 ：</strong>规则项Item<br />
  一个规则项通常用来描述Lattice中单元的状态，可以由一个或者多个原子规则项Atom构成，具有以下形式： <br />
  Atom<br />
  [Atom Atom Atom …]</p>
<p>这里：由多个原子项Atom构成时，通常加” []”表示可选其一。 </p>
<p><strong>定义4 ：</strong>规则项Item<br />
  用来描述规则项的内容，也通常指Lattice中单元Node的状态，具有以下形式 </p>
<ol>
  <li>属性判定：Att1=Val1&amp; Att2=Val2&amp;…</li>
  <li>词串: 汉字串 C 1C2.. </li>
  <li>子规则名SubRule</li>
</ol>
<p>这里： </p>
<ol>
  <li>Atom中，Att为属性名称，Val是属性的值；Att可以为词性POS、单元内容长度Len、单元内包含的动态信息Dyn，还有其他在词典中定义的属性等。 </li>
</ol>
<p>&nbsp;</p>
<ol>
  <li>如果某规则项为子规则时，需要在本规则集中，给出子规则的定义，通常形如： </li>
</ol>
<p>SubRule(<br />
  [ Item Item Item …]<br />
  )</p>
<ol>
  <li>可以在 C 1C2..前加 “^”表示离合情况，注意规则项处于第一个位置时，前面不能“^” 。注意JParser中原子规则项时，最多包含5个汉字，更多汉字时，用空格隔离 </li>
  <li>可以在“[]”前、子规则SubRule、汉字串C  1C2..前加””表示后面的内容可有可无。 </li>
</ol>
<p>&nbsp;</p>
<p><strong>定义5</strong>：  规则操作Operation<br />
  形如：Operator(Param)+ Operator(Param)+ Operator(Param)…<br />
  由一个或者多个操作函数组成，操作函数之间用“+”连接。操作函数的功能主要表示完成的动作，可以用来生成一个新单元，或者改变现有单元的属性。 <br />
  在JParser中，Operator包括如下动作函数 </p>
<ol>
  <li>Reduce  合并生成一个新单元 </li>
  <li>Boundary 为Lattice设置短语边界信息 </li>
  <li>Set 设置一个已有单元属性 </li>
  <li>Disable 对一个已有单元进行休眠处理，被休眠的单元不参加以后的操作 </li>
  <li>Enable 激活一个一休眠的单元 </li>
</ol>
<p>这里：Param是指每个动作函数的Operator的参数，不同动作函数具有不同的参数定义。 </p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<h3>有限状态自动机以及对应的序号 </h3>
<p>由以上所述，规则形式为： <br />
  .RuleName1 (Item1 Item2 Item3 … {Operation}  )<br />
  Item1 Item2 Item3 …，描述了规则的上下文，采用了类似正则表达式的表达方式，即在规则项或原子规则项中，可以包括“？” 、“ []” 和子函数。这种类似正则表达式的规则，在实际使用时，需要经过加工处理，即编译处理，编译为有限状态自动机的形式，
  自动机有一个入口、多个出口和多个内节点，出口为操作函数，内节点及对应原子规则项。从入口到出口任意路径对应原子规则项序列和这个序列对应的操作，例子见下图所示 <br />
  例如： <br />
  <img src="{{ asset('/pic/clip_image012.jpg')}}" alt="6" width="422" height="381" /> <br />
  图5 有限状态自动机例子 </p>
<p><strong>定义6</strong>： 单元序号和序号变量<br />  
<p>自动机上一个路径包括 <br />
  Entry  Atom0 Atom1 Atom2… Operation<br />
  这里：Entry是自动机入口，出口是操作函数Operation， 其中: Atom0 Atom1 Atom2…为自动机上的路径，路径上第N个Atom编号ID为N-1<br />
  我们在规则操作函数中引用的单元序号，就是指的自动机上的原子规则项的序号。<br />
  由于这是编译以后的自动机路径编号，在书写正则表达式中有时没有显式对应，在这里需要定义序号变量来表示： <br />
  例如： <br />
  .R1([我们 他们] 都  来了{Reduce(;NP=NP1)}) <br />
  规则中，“来了”原则规则项的ID可以是1或者2，不能显式得到，需要改写成： </p>
<p>.R1([我们 他们] 都  SR:$a {Reduce(;NP=NP1)})<br />
  SR(<br />
  来了 <br />
  )</p>
<p>这里”$a”就是序号变量，一个原子项规则项要设置序号变量，必须将其改写为子规则的形式。 </p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<h3>六个操作函数 </h3>
<p><strong>Reduce(Param)</strong></p>
<p>作用：如果满足了上下文条件，生成一个新单元，并设置新单元属性。 <br />
  参数Param:  Scope;Attribute；Tree;Condition;Score<br />
  这里：Reduce参数最多可包括五个部分，之间用“;”分割 <br />
  Scope：合并的单元范围，形如,其中ID是序号，或者序号变量 <br />
  ID<br />
  ID1- ID2<br />
  Attribute：设置新生成单元的属性，形如：Att=Val&amp; Att=Val，这里Att主要包括： <br />
  Head、POS、Dyn，如果属性是Head，Val就是ID形式，其他两种属性对应的Val是字符串。 <br />
  Tree：新生成单元的树结构，用括号结构表示，形如： <br />
  Node[Node/Direction:Att][ Node/Direction:Att]<br />
  这里: Node就是树单元上词或者短语，具有一下形式： <br />
  C C C、W：ID、W：ID1,ID2、T:ID、T：ID1,ID2<br />
  Direction是树枝的指向，可以向上U或者乡下D<br />
  Att，是修饰关系，表示在树的边上 <br />
  Condition： Reduce合并需要再满足的条件，通常做二元条件约束，形如： <br />
  Test1[Param]&amp; Test1[Param]&amp;…<br />
  这里：Test1为约束函数，主要具有以下函数： <br />
  <br />
  IsColl[ID1,ID2,Name]  ：ID1单元和ID2单元是否为Name类型的搭配 <br />
  IsLen[ID1,ID2] ：ID1单元和ID2单元字串是否长度一致 <br />
  IsPOS[ID1,ID2] ：ID1单元和ID2单元词性否长度一致 <br />
  IsStr[ID1,ID2] ：ID1单元和ID2单元字符串是否一致 <br />
  IsStrLeft[ID1,ID2]  ：ID1单元字符串是否为ID2单元字符串的左前缀 <br />
  IsStrRight[ID1,ID2] ：ID1单元字符串是否为ID2单元字符串的右后缀 <br />
  IsLeft[ID:A=V]：ID单元的左边是否具有A=V的属性，可以是多属性&amp;相连 <br />
  IsRight[ID:A=V]：ID单元的右边是否具有A=V的属性，可以是多属性&amp;相连 <br />
  IsOk[ID:A=V] ：ID单元的是否具有A=V的属性，可以是多属性&amp;相连 </p>
<p>  Score: Reduce函数可利用的置信度数值，是一个浮点数 <br />
  <br />
  <strong>Boundary  (Param)</strong><br />
  作用：如果满足了上下文条件，这是左右边界，为后序短语合并设置边界约束 <br />
  参数Param:  ID1，ID2；Tag；Condition<br />
  这里： <br />
  ID1和ID2是单元序号，为ID1的最左列设置“Left”边界标签，ID2单元的最右列设置“Right”边界标签； <br />
  Tag为一个表示串，不含空格，附着在Left列上 <br />
  Condition: 需要再满足的条件，同Reduce用法 </p>
<p>Set(Param)<br />
  作用：如果满足了上下文条件，为现有单元设置新的属性 <br />
  参数Param:  ID：A=V&amp; A=V；Condition<br />
  这里： <br />
  ID 单元序号 <br />
  A=V&amp; A=V需要设置的新的属性信息，可以是POS、Dyn<br />
  Condition: 需要再满足的条件，同Reduce用法 </p>
<p><strong>Disable(Param)</strong><br />
  作用：如果满足了上下文条件，设置现有单元为休眠状态 <br />
  参数Param:  Score；Condition<br />
  这里： <br />
  Scope：休眠的单元范围，形如,其中ID是序号，或者序号变量，用法同reduce中Scope<br />
  ID<br />
  ID1,ID2<br />
  Condition: 需要再满足的条件，同Reduce用法 <br />
  <br />
  <strong>Enable(Param)</strong><br />
  作用：如果满足了上下文条件，设置现有单元为激活状态 <br />
  参数Param:  Score；Condition<br />
  这里： <br />
  Scope：休眠的单元范围，形如,其中ID是序号，或者序号变量，用法同reduce中Scope<br />
  ID<br />
  ID1,ID2<br />
  Condition: 需要再满足的条件，同Reduce用法 </p>
<h2>规则示例 </h2>
<table border="1" cellspacing="0" cellpadding="0">
  <tr>
    <td width="94" valign="top"><p>1</p></td>
    <td width="597" valign="top"><p>.R(小孩 吃 苹果 <br />
      {Reduce(;POS=S;吃[小孩/D:Subj][苹果/D:Obj]<br />
      }<br />
      )</p></td>
  </tr>
  <tr>
    <td width="94" valign="top"><p>2</p></td>
    <td width="597" valign="top"><p>.R([上午 下午] POS=m    点 ？[半 ] <br />
      {Reduce(;POS=NP&amp;Dyn=Time}<br />
      )</p></td>
  </tr>
  <tr>
    <td width="94" valign="top"><p>3</p></td>
    <td width="597" valign="top"><p>.R([星期 周] [一 二 三 四 五 六 日] Tim <br />
      {Reduce(;POS=NP&amp;Dyn=Time)}{Reduce(;POS=VP}<br />
      )<br />
      Tim([上午 下午] POS=m    点 ？[半 ] )</p></td>
  </tr>
  <tr>
    <td width="94" valign="top"><p>4</p></td>
    <td width="597" valign="top"><p>.R(POS=Adj 不到哪里 去 {Reduce(;POS=VP})</p></td>
  </tr>
  <tr>
    <td width="94" valign="top"><p>5</p></td>
    <td width="597" valign="top"><p>.R(在 ^里面 <br />
      {Reduce(;POS=ADV&amp;Dyn=Inner;W:0-2[在/D:X][里面/D:x][T:1/D:Head]}<br />
      )</p></td>
  </tr>
  <tr>
    <td width="94" valign="top"><p>6</p></td>
    <td width="597" valign="top"><p>.R([POS=R POS=m] POS=q    POS=N <br />
      {Reduce(;POS=NP&amp;Head=2;W:2[W:0,1/D:Modi];IsColl(1,2,VN))<br />
      }<br />
      )</p></td>
  </tr>
  <tr>
    <td width="94" valign="top"><p>7</p></td>
    <td width="597" valign="top"><p>.R(POS=Verb POS=NP {Reduce(;POS=VP;W:0[T:1/D:Obj];IsColl(0,1,VN))})</p></td>
  </tr>
  <tr>
    <td width="94" valign="top"><p>8</p></td>
    <td width="597" valign="top"><p>.R(POS=Adj 不到哪里去    {Reduce(;POS=VP})</p></td>
  </tr>
  <tr>
    <td width="94" valign="top"><p>9</p></td>
    <td width="597" valign="top"><p>.R(在 ^里面    {Reduce(;POS=ADV&amp;Dyn=Inner;W:0,2[在/D:X][里面/D:x][T:1/D:Head]})</p></td>
  </tr>
  <tr>
    <td width="94" valign="top"><p>10</p></td>
    <td width="597" valign="top"><p>.R([POS=R POS=m] POS=q    POS=N <br />
      {Reduce(;POS=NP&amp;Head=2;W:2[W:0,1/D:Modi];IsColl(1,2,VN))}<br />
      )</p></td>
  </tr>
  <tr>
    <td width="94" valign="top"><p>11</p></td>
    <td width="597" valign="top"><p>.R(POS=Verb POS=NP <br />
      {Reduce(;POS=VP;W:0[T:1/D:Obj];IsColl(0,1,VN))}<br />
      )</p></td>
  </tr>
  <tr>
    <td width="94" valign="top"><p>12</p></td>
    <td width="597" valign="top"><p>.R(POS=Adj 的 NN:$a    {Reduce(;POS=NP&amp;Head=$a})NN(POS=Nound)<br />
      NN(<br />
      POS=Nound<br />
      )</p></td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</div>

@endsection	 