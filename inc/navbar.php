<!-- https://bootstrap-menu.com/detail-multilevel.html -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <a class="navbar-brand" href="/">Beer Me!</a>
  <form class="form-inline my-2 my-lg-0">
    <input class="form-control mr-sm-2" type="text" placeholder="Search"
           aria-label="Search">
    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">
      Search
    </button>
  </form>
  <button class="navbar-toggler" type="button" data-toggle="collapse"
          data-target="#main_nav">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="main_nav">

    <ul class="navbar-nav">
      <!--      <li class="nav-item"><a class="nav-link" href="#"> Latest Brewery Updates </a></li>-->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#"
           data-toggle="dropdown"> Beers </a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="/beerlist.php"> Beer List </a></li>
          <li><a class="dropdown-item" href="#"> Hall of Fame </a>
          </li>
          <li><a class="dropdown-item" href="#"> "What's the Best
              Brewery?" </a>
          <li><a class="dropdown-item" href="#"> Beermats, Labels,
              &amp; Pictures </a>
        </ul>
      </li>
    </ul>
  </div> <!-- navbar-collapse.// -->
</nav>