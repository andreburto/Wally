The Wally Framework
===================

<img src="https://raw.github.com/andreburto/Wally/master/wally1.png" alt="Wally" title="Wally" />

### ABOUT ###

The Wally Framework (Wally) began back in 2008 as an experiment to build my own framework.  It was influenced in part by [CodeIgniter](http://ellislab.com/codeigniter) at the time.  Wally went untouched for a few years, until I began rebuilding the LMS at work.  The first version of our LMS had been put together in 2002~2003 and worked for ten years.  It was however a Frankenstein monster that was showing its age.

In 2012~2013 when work started on the second version the half-finished Wally that I'd begun years ago came in handy.  Wally was fleshed out and integrated as part of the overall framework.

What's presented here is a cleaned up, generalized version of Wally that's used in several applications here at [WCCS](http://wccs.edu/).

### FAQ ###

*Does this code work as-is?*

Not yet. I'm still cleaning up Wally to be a general web framework.  It 90% works as-is, but you would need to tweak settings for 100%.  Hopefully I can change this answer to a single word within a week.

*Why did you write your own framework?*

During the investigative phase of last year's work I looked into using CodeIgniter or [Symfony](http://symfony.com). As I was searching around I ran into a story about someone who'd started using Symfony1 and was locked into using it after it was discontinued for Symfony2. Seeing as how the last LMS lasted a decade, I didn't want to risk getting locked into a codebase whose community development might disappear out from under me.

Building your own framework from scratch is likely the wrong path to take on most web projects.  So far it's worked out for me.

### TODO ###

1. Finish cleaning up and generalizing the code. (In progress)
2. Build a sample project that shows a working demo of Wally. (In progress)
3. Write installation and setup tutorial for working demo.
4. Make Unit tests for future expansion.
5. Go back finish documenting the code where needed.