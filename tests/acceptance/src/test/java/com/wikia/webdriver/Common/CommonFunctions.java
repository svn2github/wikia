package com.wikia.webdriver.Common;

import java.awt.AWTException;
import java.awt.Robot;
import java.io.File;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;

import com.wikia.webdriver.DriverProvider.DriverProvider;
import com.wikia.webdriver.Logging.PageObjectLogging;
import com.wikia.webdriver.Properties.Properties;

public class CommonFunctions 
{
	static By logInAjax = By.className("ajaxLogin");
	static By userNameField = By.xpath("//div[@class='input-group required   ']/input[@name='username']");
	static By passwordField = By.xpath("//div[@class='input-group required   ']/input[@name='password']");
	static By remeberMeCheckBox = By.cssSelector("input[type=checkbox]");
	static By submitButton = By.cssSelector("input[type='submit']");
	
			
	private static WebDriver driver   = DriverProvider.getWebDriver();;
	private static WebDriverWait wait = new WebDriverWait(driver, 30);
	
	
	/**
	 * log in by overlay available from main menu
	 * @param userName
	 * @param password
	 * @author: Karol Kujawiak
	 */
	
	public static void logIn(String userName, String password)
	{
		WebElement logInAjaxElem = driver.findElement(logInAjax);
		logInAjaxElem.click();
		wait.until(ExpectedConditions.presenceOfElementLocated(By.cssSelector("input[name='username']")));
		WebElement userNameFieldElem = driver.findElement(userNameField);
		userNameFieldElem.sendKeys(userName);
		WebElement passwordFieldElem = driver.findElement(passwordField);
		passwordFieldElem.sendKeys(password);
		WebElement submitButtonElem = driver.findElement(submitButton);
		submitButtonElem.click();
		wait.until(ExpectedConditions.presenceOfElementLocated(By.cssSelector("a[href*='/wiki/User:"+userName+"']")));		
	}
	
	/**
	 * 
	 * @param userName
	 * @author: Karol Kujawiak
	 */
	public static void logOut(String userName)
	{
		driver.get(Global.LIVE_DOMAIN+"wiki/Special:UserLogout?returnto=User "+userName);	
	}
	

	
	
	/**
	 * log in by overlay available from main menu, using generic credentials
	 * @author: Karol Kujawiak
	 */
	public static void logIn()
	{
		WebElement logInAjaxElem = driver.findElement(logInAjax);
		logInAjaxElem.click();
		wait.until(ExpectedConditions.presenceOfElementLocated(By.cssSelector("input[name='username']")));
		WebElement userNameFieldElem = driver.findElement(userNameField);
		userNameFieldElem.sendKeys(Properties.userName);
		WebElement passwordFieldElem = driver.findElement(passwordField);
		passwordFieldElem.sendKeys(Properties.password);
		WebElement submitButtonElem = driver.findElement(submitButton);
		submitButtonElem.click();
		wait.until(ExpectedConditions.invisibilityOfElementLocated(By.cssSelector("header#WikiaHeader a.ajaxLogin")));
		wait.until(ExpectedConditions.invisibilityOfElementLocated(By.cssSelector("header#WikiaHeader a.ajaxRegister")));
		wait.until(ExpectedConditions.presenceOfElementLocated(By.cssSelector("a[href*='/User:"+Properties.userName+"']")));
		PageObjectLogging.log("logIn ", "Normal user logged in", true, driver);
	}
	
	
	/**
	 * log in by overlay available from main menu, using generic credentials with stay logged in check-box checked
	 * @author: Karol Kujawiak
	 */
	public static void logInRememberMe()
	{
		WebElement logInAjaxElem = driver.findElement(logInAjax);
		logInAjaxElem.click();
		wait.until(ExpectedConditions.presenceOfElementLocated(By.cssSelector("input[name='username']")));
		WebElement userNameFieldElem = driver.findElement(userNameField);
		userNameFieldElem.sendKeys(Properties.userName);
		WebElement passwordFieldElem = driver.findElement(passwordField);
		passwordFieldElem.sendKeys(Properties.password);
		WebElement rememberMeCheckBox = driver.findElement(remeberMeCheckBox);
		rememberMeCheckBox.click();
		WebElement submitButtonElem = driver.findElement(submitButton);
		submitButtonElem.click();
		wait.until(ExpectedConditions.presenceOfElementLocated(By.cssSelector("a[href*='/User:"+Properties.userName+"']")));
		PageObjectLogging.log("logIn ", "Normal user logged in", true, driver);
	}
	
	/**
	 * Obsolete below method - should be fixed
	 * */
	
	public static void logInAsStaff()
	{
		WebElement logInAjaxElem = driver.findElement(logInAjax);
		logInAjaxElem.click();
		wait.until(ExpectedConditions.presenceOfElementLocated(By.cssSelector("input[name='username']")));
		WebElement userNameFieldElem = driver.findElement(userNameField);
		userNameFieldElem.sendKeys(Properties.userNameStaff);
		WebElement passwordFieldElem = driver.findElement(passwordField);
		passwordFieldElem.sendKeys(Properties.passwordStaff);
		WebElement submitButtonElem = driver.findElement(submitButton);
		submitButtonElem.click();
		wait.until(ExpectedConditions.invisibilityOfElementLocated(By.cssSelector("header#WikiaHeader a.ajaxLogin")));
		wait.until(ExpectedConditions.invisibilityOfElementLocated(By.cssSelector("header#WikiaHeader a.ajaxRegister")));
		wait.until(ExpectedConditions.presenceOfElementLocated(By.cssSelector("a[href*='/User:"+Properties.userNameStaff+"']")));
		PageObjectLogging.log("logInAsStaff ", "Staff user logged in", true, driver);
	}
	
	
	/**
	 * verifies whether pattern and current string are the same and log to log file
	 * @param pattern
	 * @param current
	 * @author: Karol Kujawiak
	 */
	public static void assertString(String pattern, String current)
	{
		try
		{
			Assert.assertEquals(pattern, current);
			PageObjectLogging.log("assertString", "pattern string: "+pattern+" <br/>current string: "+current+"<br/>are the same", true);
		}
		catch(AssertionError e)
		{
			PageObjectLogging.log("assertString", "pattern string: "+pattern+" <br/>current string: "+current+"<br/>are different", false);
		}
		
	}
	
	/**
	 * 
	 * @param attributeName
	 * @return
	 * @author: Karol Kujawiak
	 */
	public static String currentlyFocusedGetAttributeValue(String attributeName)
	{
		String currentlyFocusedName = getCurrentlyFocused().getAttribute(attributeName);
		return currentlyFocusedName;
	}
	
	/**
	 * 
	 * @param element
	 * @param attributeName
	 * @return
	 * @author: Karol Kujawiak
	 */
	public static String getAttributeValue(WebElement element, String attributeName)
	{
		return element.getAttribute(attributeName);
	}
	
	
	
	/**
	 * 
	 * @return 
	 * author: Karol Kujawiak
	 */
	public static WebElement getCurrentlyFocused()
	{
		return driver.switchTo().activeElement();
	}
	/**
	 * Scroll to the given element
	 * <p> This mehtod is used mostly because Chrome does not scroll to elements automaticly (18 july 2012)
	 * <p> This method uses JavascriptExecutor
	 * 
	 * @author Michal Nowierski
	 * @param element Webelement to be scrolled to
	*/
	public static void scrollToElement(WebElement element) {
		int y = element.getLocation().getY();
			((JavascriptExecutor)driver).executeScript("window.scrollBy(0,"+y+");");
		}
	/**
	 * Move cursor to the given X and Y coordinates
	 * 
	 * @author Michal Nowierski
	 * @param x 
	 * @param y 
	*/
	public static void MoveCursorTo(int x, int y)
	{
		Robot robot = null;
		try {
			robot = new Robot();
		} catch (AWTException e) {
			e.printStackTrace();
		}
	   robot.mouseMove(x,y);
	}

}
