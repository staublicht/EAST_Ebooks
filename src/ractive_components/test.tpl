<h1>Hello! this is var2: {{var2}}</h1>

<style>
	p { color: red; }
</style>

<script>

	// `component.exports` should basically be what you'd normally use
	// with `Ractive.extend(...)`, except that the template is already specified
	component.exports = {
		onrender: function () {
			alert( 'initing component' );
		},

		data: {
			letters: [ 'a', 'b', 'c' ]
		}
	};
</script>
