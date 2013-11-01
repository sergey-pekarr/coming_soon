	<?php $curController = Yii::app()->controller->id;
	if($curController == 'quizz') { ?>
	<span id="sidebar-quizz">
		<p class="activityselect">
			<img style="margin-top: 0px;" class="iconSnoopy" src="/images/img/quizz_icon.png">
			<strong>Quizz</strong> | <span>Beta version</span></p>							
	    <li><img class="iconSnoopy" src="/images/img/quizz1_icon.png"><a title="Quizz" href="/quizz/">Browse</a><span></span>
		</li>					
	    <li><img class="iconSnoopy" src="/images/img/quizz1_icon.png"><a title="Quizz" href="/quizz/create">New Test</a><span></span>
		</li>		
	    <li><img class="iconSnoopy" src="/images/img/quizz1_icon.png"><a title="Quizz" href="/quizz/taken">Taken</a><span></span>
		</li>	
	    <li><img class="iconSnoopy" src="/images/img/quizz1_icon.png"><a title="Quizz" href="/quizz/created">Written</a><span></span>
		</li>
		<li class="spacer"></li>		        			
	</span>
	<?php } ?>