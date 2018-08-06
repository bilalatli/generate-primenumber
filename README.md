"# generate-primenumber" 

IN <b>"primeDataConfiguration.ini"</b> file;<br>
<b><i>lastPrime : </i><b> Your biggest prime number in block files<br>
<b><i>primeCount : </i><b> Your total prime numbers in block files<br>
<b><i>primeBlockSize : </i><b> Set quantity of primenumbers for each block file<br>
<b><i>primeBlockCount : </i><b> Your total block files count<br>
<br><hr><br>
  <h2>Using : </h2>
  <span>
  &nbsp;&nbsp;&nbsp;&nbsp;require('GeneratePrime.php');<br>
  &nbsp;&nbsp;&nbsp;&nbsp;$GP = new GeneratePrime();<br>
  &nbsp;&nbsp;&nbsp;&nbsp;$info = $GP->generateBlock(); // Returns Created Block File Information<br>
  </span>
