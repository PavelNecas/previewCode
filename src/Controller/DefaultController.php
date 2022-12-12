<?php

namespace App\Controller;

use App\Model\Article;
use App\Model\News;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends BaseController
{
	/**
	 * @param Request $request
	 * @return Response
	 */
	public function defaultAction(Request $request): Response
	{
		return $this->render('default/default.html.twig');
	}

	/**
	 * @param Request $request
	 * @return Response
	 */
	public function indexAction(Request $request): Response
	{

		return $this->render('default/index.html.twig');
	}

	/**
	 * @param Request $request
	 * @param PaginatorInterface $paginator
	 * @return Response
	 */
	public function articlesAction(Request $request, PaginatorInterface $paginator): Response
	{
		$articles = new Article\Listing();
		$articles->setLocale($this->language);
		$articles->setOrderKey('createdOn');
		$articles->setOrder('desc');
		$articles->setCondition('name IS NOT ? AND name <> ?', [null, '']);

		$paginator = $paginator->paginate(
			$articles,
			$request->get('page', 1),
			6
		);

		return $this->render('default/articles.html.twig', [
			'articles' => $paginator,
			'paginationVariables' => $paginator->getPaginationData()
		]);
	}

	/**
	 * @param Request $request
	 * @return Response
	 */
	public function articleDetailAction(Request $request): Response
	{
		$articleSlug = $request->get('slug');
		$article = Article::getByUrlSlug($articleSlug, $this->language)?->getItems(0, 1)[0];

		return $this->render('default/articleDetail.html.twig', [
			'article' => $article,
			'translations' => $this->getObjectTranslations($article)
		]);
	}

	/**
	 * @param Request $request
	 * @param PaginatorInterface $paginator
	 * @return Response
	 */
	public function newsAction(Request $request, PaginatorInterface $paginator): Response
	{
		// unused in class in project
//		$newsList = new News\Listing();
//		$newsList->setLocale($this->language);
//		$newsList->setOrderKey('createdOn');
//		$newsList->setOrder('desc');
//		$newsList->setCondition('name IS NOT ? AND name <> ?', [null, '']);
//
//		$paginator = $paginator->paginate(
//			$newsList,
//			$request->get('page', 1),
//			6
//		);
//
//		return $this->render('default/news.html.twig', [
//			'newsList' => $paginator,
//			'paginationVariables' => $paginator->getPaginationData()
//		]);

		return $this->render('default/news.html.twig');
	}

	/**
	 * @param Request $request
	 * @return Response
	 */
	public function newsDetailAction(Request $request): Response
	{
		$newsSlug = $request->get('slug');
		$news = News::getByUrlSlug($newsSlug, $this->language)?->getItems(0, 1)[0];

		return $this->render('default/newsDetail.html.twig', [
			'news' => $news,
			'translations' => $this->getObjectTranslations($news)
		]);
	}

	/**
	 * @return Response
	 */
	public function ContactAction(): Response
	{
		return $this->render('default/contact.html.twig');
	}

	/**
	 * @return Response
	 */
	public function errorPageAction(): Response
	{
		return $this->render('default/errorPage.html.twig');
	}

	/**
	 * @return Response
	 */
	public function emptyPageAction(): Response
	{
		if (!$this->editmode && !\Pimcore\Tool::isFrontendRequestByAdmin()) {
			$childs = $this->document->getChildren();
			if (empty($childs)) {
				throw $this->createNotFoundException($this->translate('system_page_not_found'));
			} else {
				return $this->gotoUrl(current($childs)->getFullPath());
			}
		}

		return $this->render('default/emptyPage.html.twig');
	}

	/**
	 * @return Response
	 */
	public function mailAction(): Response
	{
		return $this->render('default/mail.html.twig');
	}

	// FORM HANDLERS
}
