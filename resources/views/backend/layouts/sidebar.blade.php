<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('admin')}}">
      <div class="sidebar-brand-icon rotate-n-15">
        <i class="fas fa-laugh-wink"></i>
      </div>
      <div class="sidebar-brand-text mx-3">
          {{Auth()->user()->roles->pluck('name')->first()}}
        </div>
    </a>
    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
      <a class="nav-link" href="{{route('admin')}}">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Tableau de Bord</span></a>
    </li>
    @role('admin')
    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Bannière
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <!-- Nav Item - Charts -->
    <li class="nav-item">
        <a class="nav-link" href="{{route('file-manager')}}">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Gestionnaire de médias</span></a>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
        <i class="fas fa-image"></i>
        <span>Bannière</span>
      </a>
      <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">Bannière Options:</h6>
          <a class="collapse-item" href="{{route('banner.index')}}">Bannière</a>
          <a class="collapse-item" href="{{route('banner.create')}}">Ajouter Bannières</a>
        </div>
      </div>
    </li>
    @endrole
    <!-- Divider -->
    <hr class="sidebar-divider">
        <!-- Heading -->
        <div class="sidebar-heading">

        </div>

    <!-- Categories -->
    @role('admin')
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#categoryCollapse" aria-expanded="true" aria-controls="categoryCollapse">
          <i class="fas fa-sitemap"></i>
          <span>Categories</span>
        </a>
        <div id="categoryCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Categories Options:</h6>
            <a class="collapse-item" href="{{route('category.index')}}">Categories</a>
            <a class="collapse-item" href="{{route('category.create')}}">Ajouter Categories</a>
          </div>
        </div>
    </li>
    @endrole
    {{-- Products --}}
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#productCollapse" aria-expanded="true" aria-controls="productCollapse">
          <i class="fas fa-cubes"></i>
          <span>Produits</span>
        </a>
        <div id="productCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Produits Options:</h6>
            <a class="collapse-item" href="{{route('product.index')}}">Produits</a>
            @role('admin|manager')
            <a class="collapse-item" href="{{route('product.create')}}">Ajouter Produit</a>
            @endrole
          </div>
        </div>
    </li>
    @role('admin')
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#stockCollapse" aria-expanded="true" aria-controls="stockCollapse">
            <i class="fas fa-sitemap"></i>
            <span>Stock</span>
            </a>
            <div id="stockCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Stock Options:</h6>
                <a class="collapse-item" href="{{route('stock.index')}}">Stock</a>
                <a class="collapse-item" href="{{route('stock.create')}}">Ajouter Stock</a>
            </div>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#boutiqueCollapse" aria-expanded="true" aria-controls="boutiqueCollapse">
            <i class="fas fa-sitemap"></i>
            <span>Boutique</span>
            </a>
            <div id="boutiqueCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">boutique Options:</h6>
                <a class="collapse-item" href="{{route('boutique.index')}}">Boutique</a>
                <a class="collapse-item" href="{{route('boutique.create')}}">Ajouter Boutique</a>
            </div>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#zoneCollapse" aria-expanded="true" aria-controls="zoneCollapse">
            <i class="fas fa-sitemap"></i>
            <span>Zone de couverture</span>
            </a>
            <div id="zoneCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Zone de couverture Options:</h6>
                <a class="collapse-item" href="{{route('zonecouverture.index')}}">Zone de couverture</a>
                <a class="collapse-item" href="{{route('zonecouverture.create')}}">Ajouter une Zone de couverture</a>
            </div>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#affectationCollapse" aria-expanded="true" aria-controls="affectationCollapse">
            <i class="fas fa-sitemap"></i>
            <span>Affectation de gerant(e) </span>
            </a>
            <div id="affectationCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Affectation de gerant</h6>
                <a class="collapse-item" href="{{route('affectation.index')}}">Affectations</a>
                <a class="collapse-item" href="{{route('affectation.create')}}">Ajouter une affectation</a>
            </div>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#approvionnementCollapse" aria-expanded="true" aria-controls="approvionnementCollapse">
            <i class="fas fa-sitemap"></i>
            <span>Approvisionnement</span>
            </a>
            <div id="approvionnementCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Approvisionnement Options:</h6>
                    <a class="collapse-item" href="{{route('approvisionnement.index')}}">Approvisionnement</a>
                    @role('admin')
                    <a class="collapse-item" href="{{route('appro.product')}}">Appro... Par produit</a>
                    @endrole
                    <a class="collapse-item" href="{{route('approvisionnement.create')}}">Ajouter un Approvisionnement</a>
                </div>
            </div>
        </li>


        {{-- Brands --}}
        {{-- <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#brandCollapse" aria-expanded="true" aria-controls="brandCollapse">
            <i class="fas fa-table"></i>
            <span>Marques</span>
            </a>
            <div id="brandCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Marques Options:</h6>
                <a class="collapse-item" href="{{route('brand.index')}}">Marques</a>
                <a class="collapse-item" href="{{route('brand.create')}}">Ajouter Marque</a>
            </div>
            </div>
        </li> --}}

    {{-- Shipping --}}
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#shippingCollapse" aria-expanded="true" aria-controls="shippingCollapse">
            <i class="fas fa-truck"></i>
            <span>Livraison</span>
            </a>
            <div id="shippingCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Livraison Options:</h6>
                <a class="collapse-item" href="{{route('shipping.index')}}">Livraison</a>
                <a class="collapse-item" href="{{route('shipping.create')}}">Ajouter Livraison</a>
            </div>
            </div>
        </li>
    @endrole
    <!--Orders -->
    <li class="nav-item">
        <a class="nav-link" href="{{route('order.index')}}">
            <i class="fas fa-hammer fa-chart-area"></i>
            <span>Commandes</span>
        </a>
    </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
          Statistique
        </div>

        <!-- Posts -->
        <li class="nav-item">
          <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#statCollapse" aria-expanded="true" aria-controls="statCollapse">
            <i class="fas fa-fw fa-folder"></i>
            <span>Vente</span>
          </a>
          <div id="statCollapse" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
              <h6 class="collapse-header">Post Options:</h6>
              <a class="collapse-item" href="{{route('order.vente')}}">Statistique Boutique</a>
              <a class="collapse-item" href="{{route('product.stat')}}">Statistique Produit</a>
            </div>
          </div>
        </li>
        @role('admin')

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    {{-- <div class="sidebar-heading">
      Posts
    </div>

    <!-- Posts -->
    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#postCollapse" aria-expanded="true" aria-controls="postCollapse">
        <i class="fas fa-fw fa-folder"></i>
        <span>Posts</span>
      </a>
      <div id="postCollapse" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">Post Options:</h6>
          <a class="collapse-item" href="{{route('post.index')}}">Posts</a>
          <a class="collapse-item" href="{{route('post.create')}}">Add Post</a>
        </div>
      </div>
    </li> --}}

     <!-- Category -->
        {{-- <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#postCategoryCollapse" aria-expanded="true" aria-controls="postCategoryCollapse">
            <i class="fas fa-sitemap fa-folder"></i>
            <span>Categories</span>
            </a>
            <div id="postCategoryCollapse" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Categories Options:</h6>
                <a class="collapse-item" href="{{route('post-category.index')}}">Categories</a>
                <a class="collapse-item" href="{{route('post-category.create')}}">Ajouter Categorie</a>
            </div>
            </div>
        </li> --}}

      <!-- Tags -->
    {{-- <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#tagCollapse" aria-expanded="true" aria-controls="tagCollapse">
            <i class="fas fa-tags fa-folder"></i>
            <span>Tags</span>
        </a>
        <div id="tagCollapse" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Tag Options:</h6>
            <a class="collapse-item" href="{{route('post-tag.index')}}">Tag</a>
            <a class="collapse-item" href="{{route('post-tag.create')}}">Add Tag</a>
            </div>
        </div>
    </li> --}}

      <!-- Comments -->
      <li class="nav-item">
        <a class="nav-link" href="{{route('comment.index')}}">
            <i class="fas fa-comments fa-chart-area"></i>
            <span>Commentaires</span>
        </a>
      </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">
     <!-- Heading -->
    <div class="sidebar-heading">
        Reglages general
    </div>
    <li class="nav-item">
      <a class="nav-link" href="{{route('coupon.index')}}">
          <i class="fas fa-table"></i>
          <span>Coupon</span></a>
    </li>
     <!-- Users -->
     <li class="nav-item">
        <a class="nav-link" href="{{route('users.index')}}">
            <i class="fas fa-users"></i>
            <span>Utilisateur</span></a>
    </li>
     <!-- General settings -->
     <li class="nav-item">
        <a class="nav-link" href="{{route('settings')}}">
            <i class="fas fa-cog"></i>
            <span>reglages</span></a>
    </li>
    @endrole
    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
      <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
