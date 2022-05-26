@extends('layouts.main')

@section('content')
    <div class="row">
        <div class="mt-5">
            <main class="blog mt-10">
                <div class="container">
                    <section class="featured-posts-section">
                        <div class="col-md-4 mb-5">
                            <!-- Widget: user widget style 2 -->
                            <div class="card card-widget widget-user-2 shadow-sm">
                                <div class="card card-warning card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            Категории
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        @foreach($categories as $category)
                                            <a href="{{ route('category.post.index', $category->id) }}" class="btn btn-info toastrDefaultInfo mb-1">
                                                    {{ $category->title }}
                                            </a>
                                        @endforeach
                                    </div>
                                    <!-- /.card -->
                                </div>

                            </div>
                            <!-- /.widget-user -->
                        </div>

                    </section>

                </div>

            </main>
        </div>
    </div>
@endsection
