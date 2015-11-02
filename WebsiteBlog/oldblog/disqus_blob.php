<html>
  
  <div id="disqus_thread"></div>
<script>
	var t = <?php echo isset($_GET['entry']) ?>;
    /**
     *  RECOMMENDED CONFIGURATION VARIABLES: EDIT AND UNCOMMENT THE SECTION BELOW TO INSERT DYNAMIC VALUES FROM YOUR PLATFORM OR CMS.
     *  LEARN WHY DEFINING THESE VARIABLES IS IMPORTANT: https://disqus.com/admin/universalcode/#configuration-variables
     */
   
    var disqus_config = function () {
        this.page.url = 'http://william.famille-blum.org/blog/index.php?entry=<?php echo $_GET[ 'entry' ] ?>';  // Replace PAGE_URL with your page's canonical URL variable
        this.page.identifier = '<?php echo  $_GET[ 'entry' ] ?>'; // Replace PAGE_IDENTIFIER with your page's unique identifier variable
    };
	
	(function() {  // DON'T EDIT BELOW THIS LINE
		if(t) {
			var d = document, s = d.createElement('script');
			
			s.src = '//williamblum.disqus.com/embed.js';
			
			s.setAttribute('data-timestamp', +new Date());
			(d.head || d.body).appendChild(s);
		}
	})();
</script>
<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript" rel="nofollow">comments powered by Disqus.</a></noscript>

</html>
